<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds product tag test data.
 */
class MW_Setup_Task_ProductAddTagTestData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData' );
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

		$this->_msg( 'Adding product tag test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'producttag.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->_addProductTagData( $testdata );

		$this->_status( 'done' );
	}

	/**
	 * Adds the product tag test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _addProductTagData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_additional, 'Default' );
		$productTagManager = $productManager->getSubManager( 'tag', 'Default' );
		$productTagTypeManager = $productTagManager->getSubManager( 'type', 'Default' );

		$typeIds = array();
		$type = $productTagTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['product/tag/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productTagTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$prodTag = $productTagManager->createItem();
		foreach( $testdata['product/tag'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product tag type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$prodTag->setId( null );
			$prodTag->setLanguageId( $dataset['langid'] );
			$prodTag->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$prodTag->setLabel( $dataset['label'] );

			$productTagManager->saveItem( $prodTag, false );
		}

		$this->_conn->commit();
	}
}