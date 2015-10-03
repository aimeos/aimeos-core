<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product stock test data.
 */
class MW_Setup_Task_ProductAddStockTestData extends MW_Setup_Task_Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductListAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex', 'MShopAddWarehouseData' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds product stock test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product stock test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'productstock.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product stock domain', $path ) );
		}

		$this->addProductStockData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the product stock test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function addProductStockData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->additional, 'Default' );
		$productStockManager = $productManager->getSubManager( 'stock', 'Default' );
		$productStockWarehouse = $productStockManager->getSubManager( 'warehouse', 'Default' );

		$prodcode = array();
		foreach( $testdata['product/stock'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['prodid'], '/' ) ) === false || ( $str = substr( $dataset['prodid'], $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for prodid are set wrong "%1$s"', $dataset['prodid'] ) );
			}

			$prodcode[] = $str;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $prodcode ) );

		$parentIds = array();
		foreach( $productManager->searchItems( $search ) as $item )
		{
			$parentIds['product/' . $item->getCode()] = $item->getId();
		}

		$wareIds = array();
		$ware = $productStockWarehouse->createItem();

		$this->conn->begin();

		foreach( $testdata['product/stock/warehouse'] as $key => $dataset )
		{
			$ware->setId( null );
			$ware->setCode( $dataset['code'] );
			$ware->setLabel( $dataset['label'] );
			$ware->setStatus( $dataset['status'] );

			$productStockWarehouse->saveItem( $ware );
			$wareIds[$key] = $ware->getId();
		}

		$stock = $productStockManager->createItem();
		foreach( $testdata['product/stock'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['prodid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product ID found for "%1$s"', $dataset['prodid'] ) );
			}

			if( !isset( $wareIds[$dataset['warehouseid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No warehouse ID found for "%1$s"', $dataset['warehouseid'] ) );
			}

			$stock->setId( null );
			$stock->setProductId( $parentIds[$dataset['prodid']] );
			$stock->setWarehouseId( $wareIds[$dataset['warehouseid']] );
			$stock->setStocklevel( $dataset['stocklevel'] );
			$stock->setDateBack( $dataset['backdate'] );

			$productStockManager->saveItem( $stock, false );
		}

		$this->conn->commit();
	}
}