<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product tag test data.
 */
class MW_Setup_Task_ProductAddTagTestData extends MW_Setup_Task_Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData' );
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
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product tag test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'producttag.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$this->addProductTagData( $testdata );

		$this->status( 'done' );
	}

	/**
	 * Adds the product tag test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function addProductTagData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->additional, 'Default' );
		$productTagManager = $productManager->getSubManager( 'tag', 'Default' );
		$productTagTypeManager = $productTagManager->getSubManager( 'type', 'Default' );

		$typeIds = array();
		$type = $productTagTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['product/tag/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$productTagTypeManager->saveItem( $type );
			$typeIds[$key] = $type->getId();
		}

		$prodTag = $productTagManager->createItem();
		foreach( $testdata['product/tag'] as $key => $dataset )
		{
			if( !isset( $typeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product tag type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$prodTag->setId( null );
			$prodTag->setLanguageId( $dataset['langid'] );
			$prodTag->setTypeId( $typeIds[$dataset['typeid']] );
			$prodTag->setLabel( $dataset['label'] );

			$productTagManager->saveItem( $prodTag, false );
		}

		$this->conn->commit();
	}
}