<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Product suggestions job controller.
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Product suggestions' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Generation of product suggestions' );
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




		$prodMgr = MShop_Product_Manager_Factory::createManager( $context );
		$prodListMgr = $prodMgr->getSubManager('list');
		$listTypeId = $this->_getProductListTypeId( $prodListMgr, 'suggestion' );

		$baseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$search = $baseProductManager->createSearch();
		$config->set( 'aggregatekey', 'mshop/order/manager/base/product/default/aggregateAllProductsInOrders' );

		$totalsCnt = $baseProductManager->aggregate( $search, 'order.base.product.productid' );



		$search = $baseProductManager->createSearch();
		$context->getConfig()->set( 'aggregatekey', 'mshop/order/manager/base/product/default/countAll' );
		$ordersCnt = $baseProductManager->aggregate( $search, 'order.base.product.baseid' );
		$totalOrders = $ordersCnt[0];

		$minSupport = $config->get( 'controller/jobs/product/suggestions/minsupport', 0.1 );

		$filteredBySupport = array();
		foreach( $totalsCnt as $id => $totalCnt )
		{
			if( $minSupport <= ( $sup = ( $totalCnt / $totalOrders ) ) ) {
				$filteredBySupport[$id] = $sup;
			}
		}

		$minConfidence = $config->get( 'controller/jobs/product/suggestions/minconfidence', 0.3 );
		$maxSuggestions = $config->get( 'controller/jobs/product/suggestions/maxsuggestions', 10 );

		foreach( $filteredBySupport as $id => $sup )
		{
			$prodList = array();

			$search = $baseProductManager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.product.productid', $id ) );
			$this->_getContext()->getConfig()->set( 'aggregatekey', 'mshop/order/manager/base/product/default/aggregateProductsInOrders' );
			$relativeCnt = $baseProductManager->aggregate($search, 'order.base.product.productid');

			unset( $relativeCnt[$id] );

			foreach( $relativeCnt as $relId => $relCnt ) {
				if ($minSupport <= ($relCnt / $totalOrders))
				{
					if( $minConfidence <= ( $conf = ( $relCnt / $totalsCnt[$id] ) ) ) {
						$prodList[ $relId ] = $conf;
					}
				}
			}

			arsort( $prodList );

			$prodList = array_slice( $prodList, 0, $maxSuggestions, true );

			$this->_removeProductSuggestions( $id, $listTypeId, $prodListMgr );
			$this->_createProductListSuggestions( $id, array_keys( $prodList ), $listTypeId, $prodListMgr );
		}
	}

	/**
	 * Gets or creates product list type item with specified code.
	 *
	 * @param MShop_Product_Manager_List_Type_Default $prodListMgr Manager for list types of product domain
	 * @param string $code Code of product list type
	 */
	protected function _getProductListTypeId( $prodListMgr, $code )
	{
		$prodListTypeMgr = $prodListMgr->getSubManager('type');

		$search = $prodListTypeMgr->createSearch();
		$search->setConditions( $search->compare( '==', 'product.list.type.code', $code ) );
		$items = $prodListTypeMgr->searchItems( $search );

		if( ( $suggestionType = reset( $items ) ) == false  ) {
			//create suggestion list type
			$suggestionType = $prodListTypeMgr->createItem();
			$suggestionType->setCode( $code );
			$suggestionType->setLabel( ucfirst( $code ) );
			$suggestionType->setDomain( 'product' );
			$suggestionType->setStatus( 1 );
			$suggestionType->setSiteId( $this->_getContext()->getLocale()->getSiteId() );

			$prodListTypeMgr->saveItem( $suggestionType );
		}

		return $suggestionType->getId();
	}


	/**
	 * Adds products to product list as suggested.
	 *
	 * @param array $suggestedIds List with Id => Ids => %relevance
	 */
	protected function _createProductListSuggestions( $productId, array $productList, $listTypeId, $prodListMgr )
	{
		if( empty( $productList ) ) {
			return;
		}

		$listItem = $prodListMgr->createItem();
		foreach( $productList as $key => $id )
		{
			$listItem->setId(null);
			$listItem->setParentId( $productId );
			$listItem->setDomain( 'product' );
			$listItem->setTypeId( $listTypeId );
			$listItem->setRefId( $id );
			$listItem->setStatus( 1 );
			$listItem->setPosition( $key );

			$prodListMgr->saveItem( $listItem );
		}
	}


	/**
	 * Optionally first remove all suggested products from product list.
	 *
	 * @param array $suggestedIds List with Id => Ids => %relevance
	 */
	protected function _removeProductSuggestions( $productId, $listTypeId, $prodListMgr )
	{
		$search = $prodListMgr->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.parentid', $productId ),
			$search->compare( '==', 'product.list.type.id', $listTypeId )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$listItems = $prodListMgr->searchItems( $search );

		$prodListMgr->deleteItems( array_keys( $listItems ) );
	}
}
