<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Adds product property test data.
 */
class MW_Setup_Task_ProductAddPropertyTestData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds product test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product property test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'productproperty.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->addProductPropertyData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the product property test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function addProductPropertyData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->additional, 'Default' );
		$productPropertyManager = $productManager->getSubManager( 'property', 'Default' );
		$productPropertyTypeManager = $productPropertyManager->getSubManager( 'type', 'Default' );

		$typeIds = array();
		$type = $productPropertyTypeManager->createItem();
		$prodIds = $this->getProductIds( $productManager );
		
		$this->conn->begin();

		foreach( $testdata['product/property/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productPropertyTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$prodProperty = $productPropertyManager->createItem();
		foreach( $testdata['product/property'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product property type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$prodProperty->setId( null );
			$prodProperty->setParentId( $prodIds[ $dataset['parentid'] ] );
			$prodProperty->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$prodProperty->setLanguageId( $dataset['langid'] );
			$prodProperty->setValue( $dataset['value'] );

			$productPropertyManager->saveItem( $prodProperty, false );
		}

		$this->conn->commit();
	}
	

	/**
	 * Retrieves the product IDs for the used codes
	 * 
	 * @param MShop_Common_Manager_Interface $productManager Product manager object
	 * @return array Associative list of product codes as key (e.g. product/CNC) and IDs as value
	 */
	protected function getProductIds( MShop_Common_Manager_Interface $productManager )
	{
		$entry = array();
		$search = $productManager->createSearch();

		foreach( $productManager->searchItems( $search ) as $id => $item ) {
			$entry[ 'product/' . $item->getCode() ] = $id;
		}
		
		return $entry;
		
	}
}