<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogSiteAddTestData.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Adds catalog list test data and all items from other domains.
 */
class MW_Setup_Task_CatalogSiteAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'ProductListAddTestData', 'CatalogAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'LogAddTestData' );
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

		$this->_msg('Adding catalog-site test data', 0);
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'catalog-site.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for catalog site table', $path ) );
		}

		$this->_addCatalogSiteData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the catalog site test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addCatalogSiteData( array $testdata )
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional, 'Default' );
		$catalogSiteManager = $catalogManager->getSubManager( 'site' );

		$itemCode = array();
		foreach( $testdata['catalog/site'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$itemCode[] = $str;
		}

		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $itemCode) );

		$parentIds = array();
		foreach( $catalogManager->searchItems( $search ) as $item )	{
			$parentIds[ 'catalog/'.$item->getCode() ] = $item->getId();
		}

		$site = $catalogSiteManager->createItem();
		foreach( $testdata['catalog/site'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No catalog ID found for "%1$s"', $dataset['parentid'] ) );
			}

			$site->setId( null );
			$site->setParentId( $parentIds[ $dataset['parentid'] ] );
			$site->setValue( $dataset['value'] );

			$catalogSiteManager->saveItem( $site, false );
		}
	}
}