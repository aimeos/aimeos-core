<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductAddTestData.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Adds product test data.
 */
class MW_Setup_Task_ProductAddTestData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'MediaListAddTestData', 'PriceListAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds product test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding product test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'product.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$parentIds = $this->_addProductData( $testdata );

		$this->_status( 'done' );
	}



	/**
	 * Adds the product test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _addProductData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_additional, 'Default' );
		$productTypeManager = $productManager->getSubManager( 'type', 'Default' );

		$typeIds = array();
		$type = $productTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['product/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$product = $productManager->createItem();
		foreach( $testdata['product'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$product->setId( null );
			$product->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$product->setCode( $dataset['code'] );
			$product->setLabel( $dataset['label'] );
			$product->setSupplierCode( $dataset['suppliercode'] );
			$product->setStatus( $dataset['status'] );

			$productManager->saveItem( $product, false );
		}

		$this->_conn->commit();
	}
}