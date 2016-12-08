<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to stock table.
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
	 * Insert stock data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding stock performance data', 0 );


		$context = $this->getContext();

		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$stockManager = \Aimeos\MShop\Factory::createManager( $context, 'stock' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $context, 'stock/type' );


		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'stock.type.code', 'default' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No type with code "default" found' );
		}


		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$start = 0;
		$typeid = $typeItem->getId();
		$item = $stockManager->createItem();
		$stocklevels = array( null, 100, 80, 60, 40, 20, 10, 5, 2, 0 );


		do
		{
			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $product )
			{
				$item->setId( null );
				$item->setTypeId( $typeid );
				$item->setProductCode( $product->getCode() );
				$item->setStockLevel( current( $stocklevels ) );
				$stockManager->saveItem( $item );

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
