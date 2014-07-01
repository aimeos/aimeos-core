<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds catalog list test data and all items from other domains.
 */
class MW_Setup_Task_CatalogListAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductListAddTestData', 'CatalogAddTestData' );
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
	 * Adds catalog test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg('Adding catalog-list test data', 0);
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'catalog-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for catalog list domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['catalog/list'] as $dataset ) {
			$refKeys[ $dataset['domain'] ][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['text'] = $this->_getTextData( $refKeys['text'] );
		$refIds['media'] = $this->_getMediaData( $refKeys['media'] );
		$refIds['product'] = $this->_getProductData( $refKeys['product'] );

		$this->_addCatalogListData( $testdata, $refIds );

		$this->_status( 'done' );
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _getTextData( array $keys )
	{
		$textManager = MShop_Text_Manager_Factory::createManager( $this->_additional, 'Default' );

		$labels = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = array();
		foreach( $textManager->searchItems( $search ) as $item )	{
			$refIds[ 'text/'.$item->getLabel() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _getMediaData( array $keys )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->_additional, 'Default' );

		$urls = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$refIds = array();
		foreach( $mediaManager->searchItems( $search ) as $item ) {
			$refIds[ 'media/'.$item->getUrl() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required product item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no product is found
	 */
	private function _getProductData( array $keys )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_additional, 'Default' );

		$codes = array();
		foreach( $keys as $dataset)
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref product are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		$refIds = array();
		foreach( $productManager->searchItems( $search ) as $item ) {
			$refIds[ 'product/'.$item->getCode() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addCatalogListData( array $testdata, array $refIds )
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional, 'Default' );
		$catalogListManager = $catalogManager->getSubManager( 'list', 'Default' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type', 'Default' );

		$itemCode = array();
		foreach( $testdata['catalog/list'] as $dataset )
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

		$listItemTypeIds = array();
		$listItemType = $catalogListTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['catalog/list/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$catalogListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[ $key ] = $listItemType->getId();
		}

		$listItem = $catalogListManager->createItem();
		foreach( $testdata['catalog/list'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No catalog ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[ $dataset['domain'] ][ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			if( !isset( $listItemTypeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No catalog list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[ $dataset['parentid'] ] );
			$listItem->setTypeId( $listItemTypeIds[ $dataset['typeid'] ] );
			$listItem->setRefId( $refIds[ $dataset['domain']][$dataset['refid'] ] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$catalogListManager->saveItem( $listItem, false );
		}

		$this->_conn->commit();
	}
}