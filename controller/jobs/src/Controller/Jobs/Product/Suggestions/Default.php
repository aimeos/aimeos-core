<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
<<<<<<< Updated upstream
 * Product suggestions job controller.
=======
 * Job controller for bought together products.
>>>>>>> Stashed changes
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Suggestions_Default
	extends Controller_Jobs_Abstract
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Products bought together' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Creates bought together product suggestions' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();
		$config = $context->getConfig();


		/** controller/jobs/product/suggestions/max-items
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
		 * @see controller/jobs/product/suggestions/min-support
		 * @see controller/jobs/product/suggestions/min-confidence
		 * @see controller/jobs/product/suggestions/limit-days
		 */
		$maxItems = $config->get( 'controller/jobs/product/suggestions/max-items', 5 );

		/** controller/jobs/product/suggestions/min-support
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
		 * @see controller/jobs/product/suggestions/max-items
		 * @see controller/jobs/product/suggestions/min-confidence
		 * @see controller/jobs/product/suggestions/limit-days
		 */
		$minSupport = $config->get( 'controller/jobs/product/suggestions/min-support', 0.02 );

		/** controller/jobs/product/suggestions/min-confidence
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
		 * @see controller/jobs/product/suggestions/max-items
		 * @see controller/jobs/product/suggestions/min-support
		 * @see controller/jobs/product/suggestions/limit-days
		 */
		$minConfidence = $config->get( 'controller/jobs/product/suggestions/min-confidence', 0.66 );

		/** controller/jobs/product/suggestions/limit-days
		 * Only send delivery e-mails of orders that were created in the past within the configured number of days
		 *
		 * The delivery e-mails are normally send immediately after the delivery
		 * status has changed. This option prevents e-mails for old order from
		 * being send in case anything went wrong or an update failed to avoid
		 * confusion of customers.
		 *
		 * @param integer Number of days
		 * @since 2014.09
		 * @category User
		 * @category Developer
		 * @see controller/jobs/product/suggestions/max-items
		 * @see controller/jobs/product/suggestions/min-support
		 * @see controller/jobs/product/suggestions/min-confidence
		 */
		$days = $config->get( 'controller/jobs/product/suggestions/limit-days', 180 );
		$date = date( 'Y-m-d H:i:s', time() - $days * 86400 );


		$typeItem = $this->_getTypeItem( 'product/list/type', 'product', 'bought-together' );

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
				$this->_removeListItems( $id, $typeItem->getId() );

				if( $count / $totalOrders > $minSupport )
				{
					$productIds = $this->_getSuggestions( $id, $prodIds, $count, $totalOrders, $maxItems, $minSupport, $minConfidence, $date );
					$this->_addListItems( $id, $typeItem->getId(), $productIds );
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
	protected function _getSuggestions( $id, $prodIds, $count, $total, $maxItems, $minSupport, $minConfidence )
	{
		$baseProductManager = MShop_Factory::createManager( $this->_getContext(), 'order/base/product' );

		$search = $baseProductManager->createSearch();
		$func = $search->createFunction( 'order.base.product.count', array( $id ) );
		$expr = array(
			$search->compare( '==', 'order.base.product.productid', $prodIds ),
			$search->compare( '==', 'order.base.product.ctime', $date ),
			$search->compare( '==', $func, 1 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$relativeCounts = $baseProductManager->aggregate( $search, 'order.base.product.productid' );

		unset( $relativeCounts[$id] );
		$supportA = $count / $total;
		$products = array();

		foreach( $relativeCounts as $prodId => $relCnt )
		{
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
	protected function _addListItems( $productId, $typeId, array $productIds )
	{
		if( empty( $productList ) ) {
			return;
		}

		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/list' );
		$item = $manager->createItem();

		foreach( $productIds as $pos => $refid )
		{
			$listItem->setId( null );
			$listItem->setParentId( $productId );
			$listItem->setDomain( 'product' );
			$listItem->setTypeId( $typeId );
			$listItem->setPosition( $pos );
			$listItem->setRefId( $refid );
			$listItem->setStatus( 1 );

			$manager->saveItem( $item );
		}
	}


	/**
	 * Remove all suggested products from product list.
	 *
	 * @param string $productId Unique ID of the product the references should be removed from
	 * @param integer $typeId Unique ID of the list type the referenced products should be removed from
	 */
	protected function _removeListItems( $productId, $typeId )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/list' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.parentid', $productId ),
			$search->compare( '==', 'product.list.domain', 'product' ),
			$search->compare( '==', 'product.list.typeid', $typeId ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$listItems = $productsMgr->searchItems( $search );

		$manager->deleteItems( array_keys( $listItems ) );
	}
}
