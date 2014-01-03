<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ProductAddStockPerfData extends MW_Setup_Task_ProductAddBasePerfData
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
		$this->_msg('Adding product stock performance data', 0);


		$context =  $this->_getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productStockManager = $productManager->getSubManager( 'stock' );

		$item = $productStockManager->createItem();
		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$start = 0;
		$stocklevel = 1;


		$this->_txBegin();

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $product )
			{
				$item->setId( null );
				$item->setProductId( $id );
				$item->setStockLevel( $stocklevel++ );
				$productStockManager->saveItem( $item );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->_txCommit();


		$this->_status( 'done' );
	}
}
