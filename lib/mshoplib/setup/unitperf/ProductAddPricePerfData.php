<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ProductAddPricePerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert price data and product/price relations.
	 */
	protected function _process()
	{
		$this->_msg('Adding product price performance data', 0);


		$context =  $this->_getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'price');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$priceTypeManager = $priceManager->getSubManager( 'type' );

		$expr = array();
		$search = $priceTypeManager->createSearch();
		$expr[] = $search->compare('==', 'price.type.domain', 'product');
		$expr[] = $search->compare('==', 'price.type.code', 'default' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $priceTypeManager->searchItems($search);

		if ( ($priceTypeItem = reset($types)) === false) {
			throw new Exception('Price type item not found');
		}


		$priceItem = $priceManager->createItem();
		$priceItem->setTypeId( $priceTypeItem->getId() );
		$priceItem->setDomain( 'product' );
		$priceItem->setCurrencyId( 'EUR' );
		$priceItem->setShipping( '0.00' );
		$priceItem->setTaxRate( '20.00' );
		$priceItem->setQuantity( 1 );
		$priceItem->setStatus( 1 );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'price' );
		$listItem->setPosition( 0 );


		$this->_txBegin();

		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$start = 0;
		$price = 1000;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $item )
			{
				$priceItem->setId( null );
				$priceItem->setValue( $price );
				$priceItem->setRebate( $price / 10 );
				$priceManager->saveItem( $priceItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $priceItem->getId() );
				$productListManager->saveItem( $listItem, false );

				if( --$price < 1 ) {
					$price = 1000;
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		$this->_txCommit();


		$this->_status( 'done' );
	}
}
