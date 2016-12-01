<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to product table.
 */
class ProductAddStockPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
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
	public function migrate()
	{
		$this->msg( 'Adding product stock performance data', 0 );


		$context = $this->getContext();

		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$productStockManager = $productManager->getSubManager( 'stock' );
		$productTypeManager = $productStockManager->getSubManager( 'type' );


		$search = $productTypeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.type.code', 'default' ) );
		$result = $productTypeManager->searchItems( $search );

		if( ( $whItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No type with code "default" found' );
		}


		$item = $productStockManager->createItem();
		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$start = 0;
		$typeid = $whItem->getId();
		$stocklevels = array( null, 100, 80, 60, 40, 20, 10, 5, 2, 0 );


		do
		{
			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $id => $product )
			{
				$item->setId( null );
				$item->setParentId( $id );
				$item->setTypeId( $typeid );
				$item->setStockLevel( current( $stocklevels ) );
				$productStockManager->saveItem( $item );

				if( next( $stocklevels ) === false ) {
					reset( $stocklevels );
				}
			}

			$this->txCommit();

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 1000 );
		}
		while( $count == $search->getSliceSize() );


		$this->status( 'done' );
	}
}
