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
	protected function process()
	{
		$this->msg( 'Adding product stock performance data', 0 );


		$context = $this->getContext();

		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$productStockManager = $productManager->getSubManager( 'stock' );
		$productWarehouseManager = $productStockManager->getSubManager( 'warehouse' );


		$search = $productWarehouseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.code', 'default' ) );
		$result = $productWarehouseManager->searchItems( $search );

		if( ( $whItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No warehouse with code "default" found' );
		}


		$item = $productStockManager->createItem();
		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$start = 0;
		$warehouseid = $whItem->getId();
		$stocklevels = array( null, 100, 80, 60, 40, 20, 10, 5, 2, 0 );


		$this->txBegin();

		do
		{
			$result = $productManager->searchItems( $search );

			foreach( $result as $id => $product )
			{
				$item->setId( null );
				$item->setProductId( $id );
				$item->setWarehouseId( $warehouseid );
				$item->setStockLevel( current( $stocklevels ) );
				$productStockManager->saveItem( $item );

				if( next( $stocklevels ) === false ) {
					reset( $stocklevels );
				}
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
