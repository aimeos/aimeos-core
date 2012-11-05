<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductSiteAddTestData.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Adds product site test data.
 */
class MW_Setup_Task_ProductSiteAddTestData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData', 'ProductAddTagTestData' );
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
	 * Adds product site test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding product-site test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'product-site.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product site table', $path ) );
		}

		$this->_addProductSiteData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the product-site test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _addProductSiteData( array $testdata )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_additional, 'Default' );
		$productSiteManager = $productManager->getSubManager( 'site', 'Default' );

		$itemCodes = array();
		foreach( $testdata['product/site'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$itemCodes[] = $str;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $itemCodes ) );

		$parentIds = array();
		foreach( $productManager->searchItems( $search ) as $item ) {
			$parentIds[ 'product/'.$item->getCode() ] = $item->getId();
		}

		$site = $productSiteManager->createItem();
		foreach( $testdata['product/site'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product ID found for "%1$s" in site test data', $dataset['parentid'] ) );
			}

			$site->setId( null );
			$site->setParentId( $parentIds[ $dataset['parentid'] ] );
			$site->setValue( $dataset['value'] );

			$productSiteManager->saveItem( $site, false );
		}
	}
}