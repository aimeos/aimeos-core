<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Job controller for bought together products.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Bought_Default
	extends Controller_Jobs_Base
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Products bought together' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Creates bought together product suggestions' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$config = $context->getConfig();


		/** controller/jobs/product/bought/max-items
		 * Maximum number of suggested items per product
		 *
		 * Each product can contain zero or more suggested products based on
		 * the used algorithm. The maximum number of items limits the quantity
		 * of products that are associated as suggestions to one product.
		 * Usually, you don't need more products than shown in the product
		 * detail view as suggested products.
		 *
		 * @param integer Number of suggested products
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/bought/min-support
		 * @see controller/jobs/product/bought/min-confidence
		 * @see controller/jobs/product/bought/limit-days
		 */
		$maxItems = $config->get( 'controller/jobs/product/bought/max-items', 5 );

		/** controller/jobs/product/bought/min-support
		 * Minimum support value to sort out all irrelevant combinations
		 *
		 * A minimum support value of 0.02 requires the combination of two
		 * products to be in at least 2% of all orders to be considered relevant
		 * enough as product suggestion.
		 *
		 * You can tune this value for your needs, e.g. if you sell several
		 * thousands different products and you have only a few suggestions for
		 * all products, a lower value might work better for you. The other way
		 * round, if you sell less than thousand different products, you may
		 * have a lot of product suggestions of low quality. In this case it's
		 * better to increase this value, e.g. to 0.05 or higher.
		 *
		 * Caution: Decreasing the support to lower values than 0.01 exponentially
		 * increases the time for generating the suggestions. If your database
		 * contains a lot of orders, the time to complete the job may rise from
		 * hours to days!
		 *
		 * @param float Minimum support value from 0 to 1
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/bought/max-items
		 * @see controller/jobs/product/bought/min-confidence
		 * @see controller/jobs/product/bought/limit-days
		 */
		$minSupport = $config->get( 'controller/jobs/product/bought/min-support', 0.02 );

		/** controller/jobs/product/bought/min-confidence
		 * Minimum confidence value for high quality suggestions
		 *
		 * The confidence value is used to remove low quality suggestions. Using
		 * a confidence value of 0.95 would only suggest product combinations
		 * that are almost always bought together. Contrary, a value of 0.1 would
		 * yield a lot of combinations that are bought together only in very rare
		 * cases.
		 *
		 * To get good product suggestions, the value should be at least above
		 * 0.5 and the higher the value, the better the suggestions. You can
		 * either increase the default value to get better suggestions or lower
		 * the value to get more suggestions per product if you have only a few
		 * ones in total.
		 *
		 * @param float Minimum confidence value from 0 to 1
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/bought/max-items
		 * @see controller/jobs/product/bought/min-support
		 * @see controller/jobs/product/bought/limit-days
		 */
		$minConfidence = $config->get( 'controller/jobs/product/bought/min-confidence', 0.66 );

		/** controller/jobs/product/bought/limit-days
		 * Only use orders placed in the past within the configured number of days for calculating bought together products
		 *
		 * This option limits the orders that are evaluated for calculating the
		 * bought together products. Only ordered products that were bought by
		 * customers within the configured number of days are used.
		 *
		 * Limiting the orders taken into account to the last ones increases the
		 * quality of suggestions if customer interests shifts to new products.
		 * If you only have a few orders per month, you can also increase this
		 * value to several years to get enough suggestions. Please keep in mind
		 * that the more orders are evaluated, the longer the it takes to
		 * calculate the product combinations.
		 *
		 * @param integer Number of days
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see controller/jobs/product/bought/max-items
		 * @see controller/jobs/product/bought/min-support
		 * @see controller/jobs/product/bought/min-confidence
		 */
		$days = $config->get( 'controller/jobs/product/bought/limit-days', 180 );
		$date = date( 'Y-m-d H:i:s', time() - $days * 86400 );


		$typeItem = $this->getTypeItem( 'product/list/type', 'product', 'bought-together' );

		$baseManager = MShop_Factory::createManager( $context, 'order/base' );
		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '>', 'order.base.ctime', $date ) );
		$search->setSlice( 0, 0 );
		$totalOrders = 0;
		$baseManager->searchItems( $search, array(), $totalOrders );

		$baseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$search = $baseProductManager->createSearch();
		$search->setConditions( $search->compare( '>', 'order.base.product.ctime', $date ) );
		$start = 0;

		do
		{
			$totalCounts = $baseProductManager->aggregate( $search, 'order.base.product.productid' );
			$prodIds = array_keys( $totalCounts );

			foreach( $totalCounts as $id => $count )
			{
				$this->removeListItems( $id, $typeItem->getId() );

				if( $count / $totalOrders > $minSupport )
				{
					$productIds = $this->getSuggestions( $id, $prodIds, $count, $totalOrders, $maxItems, $minSupport, $minConfidence, $date );
					$this->addListItems( $id, $typeItem->getId(), $productIds );
				}
			}

			$count = count( $totalCounts );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );
	}


	/**
	 * Returns the IDs of the suggested products.
	 *
	 * @param string $id Product ID to calculate the suggestions for
	 * @param string[] $prodIds List of product IDs to create suggestions for
	 * @param integer $count Number of ordered products
	 * @param integer $total Total number of orders
	 * @param integer $maxItems Maximum number of suggestions
	 * @param float $minSupport Minium support value for calculating the suggested products
	 * @param float $minConfidence Minium confidence value for calculating the suggested products
	 * @param string $date Date in YYYY-MM-DD HH:mm:ss format after which orders should be used for calculations
	 * @return array List of suggested product IDs as key and their confidence as value
	 */
	protected function getSuggestions( $id, $prodIds, $count, $total, $maxItems, $minSupport, $minConfidence, $date )
	{
		$refIds = array();
		$context = $this->getContext();

		$catalogListManager = MShop_Factory::createManager( $context, 'catalog/list' );
		$baseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );


		$search = $baseProductManager->createSearch();
		$func = $search->createFunction( 'order.base.product.count', array( (string) $id ) );
		$expr = array(
			$search->compare( '==', 'order.base.product.productid', $prodIds ),
			$search->compare( '>', 'order.base.product.ctime', $date ),
			$search->compare( '==', $func, 1 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$relativeCounts = $baseProductManager->aggregate( $search, 'order.base.product.productid' );


		$search = $catalogListManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.refid', array_keys( $relativeCounts ) ),
			$search->compare( '==', 'catalog.list.domain', 'product' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $catalogListManager->searchItems( $search ) as $listItem ) {
			$refIds[$listItem->getRefId()] = true;
		}


		unset( $relativeCounts[$id] );
		$supportA = $count / $total;
		$products = array();

		foreach( $relativeCounts as $prodId => $relCnt )
		{
			if( !isset( $refIds[$prodId] ) ) {
				continue;
			}

			$supportAB = $relCnt / $total;

			if( $supportAB > $minSupport && ( $conf = ( $supportAB / $supportA ) ) > $minConfidence ) {
				$products[$prodId] = $conf;
			}
		}

		arsort( $products );

		return array_keys( array_slice( $products, 0, $maxItems, true ) );
	}


	/**
	 * Adds products as referenced products to the product list.
	 *
	 * @param string $productId Unique ID of the product the given products should be referenced to
	 * @param integer $typeId Unique ID of the list type used for the referenced products
	 * @param array $productIds List of position as key and product ID as value
	 */
	protected function addListItems( $productId, $typeId, array $productIds )
	{
		if( empty( $productIds ) ) {
			return;
		}

		$manager = MShop_Factory::createManager( $this->getContext(), 'product/list' );
		$item = $manager->createItem();

		foreach( $productIds as $pos => $refid )
		{
			$item->setId( null );
			$item->setParentId( $productId );
			$item->setDomain( 'product' );
			$item->setTypeId( $typeId );
			$item->setPosition( $pos );
			$item->setRefId( $refid );
			$item->setStatus( 1 );

			$manager->saveItem( $item );
		}
	}


	/**
	 * Remove all suggested products from product list.
	 *
	 * @param string $productId Unique ID of the product the references should be removed from
	 * @param integer $typeId Unique ID of the list type the referenced products should be removed from
	 */
	protected function removeListItems( $productId, $typeId )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'product/list' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.parentid', $productId ),
			$search->compare( '==', 'product.list.domain', 'product' ),
			$search->compare( '==', 'product.list.typeid', $typeId ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$listItems = $manager->searchItems( $search );

		$manager->deleteItems( array_keys( $listItems ) );
	}
}
