<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to product table.
 */
class ProductAddPricePerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert price data and product/price relations.
	 */
	protected function process()
	{
		$this->msg( 'Adding product price performance data', 0 );


		$context = $this->getContext();


		$priceTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'price/type' );

		$expr = array();
		$search = $priceTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'price.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'price.type.code', 'default' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $priceTypeManager->searchItems( $search );

		if( ( $priceTypeItem = reset( $types ) ) === false ) {
			throw new \Exception( 'Price type item not found' );
		}


		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );

		$priceItem = $priceManager->createItem();
		$priceItem->setTypeId( $priceTypeItem->getId() );
		$priceItem->setDomain( 'product' );
		$priceItem->setCurrencyId( 'EUR' );
		$priceItem->setCosts( '0.00' );
		$priceItem->setTaxRate( '20.00' );
		$priceItem->setQuantity( 1 );
		$priceItem->setStatus( 1 );


		$productListTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists/type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'default' );
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'price' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems( $search );

		if( ( $listTypeItem = reset( $types ) ) === false ) {
			throw new \Exception( 'Product list type item not found' );
		}


		$productListManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'price' );
		$listItem->setPosition( 0 );


		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$start = 0;
		$price = 100;
		$value = +1;

		$this->txBegin();

		do
		{
			$result = $productManager->searchItems( $search );

			foreach( $result as $id => $item )
			{
				$listItem->setParentId( $id );

				for( $i = 0; $i < 3; $i++ )
				{
					$priceItem->setId( null );
					$priceItem->setLabel( $item->getLabel() . ': from ' . ( 1 + $i * 5 ) );
					$priceItem->setQuantity( 1 + $i * 5 );
					$priceItem->setValue( $price - $i * 10 );
					$priceItem->setRebate( $i * 10 );
					$priceManager->saveItem( $priceItem );

					$listItem->setId( null );
					$listItem->setPosition( $i );
					$listItem->setRefId( $priceItem->getId() );
					$productListManager->saveItem( $listItem, false );
				}

				if( $price >= 999 ) {
					$value = -1;
				} else if( $price <= 100 ) {
					$value = +1;
				}

				$price += $value;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->txCommit();


		$this->status( 'done' );
	}
}
