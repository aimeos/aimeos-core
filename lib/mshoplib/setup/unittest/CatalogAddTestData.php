<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds catalog test data.
 */
class MW_Setup_Task_CatalogAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductListAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds catalog test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg('Adding catalog test data', 0);
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'catalog.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
		}

		$this->_addCatalogData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addCatalogData( array $testdata )
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional, 'Default' );

		$parentIds = arrayy( 'init' => null );
		$catalog = $catalogManager->createItem();

		foreach( $testdata['catalog'] as $key => $dataset )
		{
			$catalog->setId( null );
			$catalog->setCode( $dataset['code'] );
			$catalog->setLabel( $dataset['label'] );
			$catalog->setConfig( $dataset['config'] );
			$catalog->setStatus( $dataset['status'] );

			$catalogManager->insertItem( $catalog, $parentIds[ $dataset['parent'] ] );
			$parentIds[ $key ] = $catalog->getId();
		}
	}
}