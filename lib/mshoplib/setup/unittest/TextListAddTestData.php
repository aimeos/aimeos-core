<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds text test data.
 */
class MW_Setup_Task_TextListAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'PriceListAddTestData', 'TextAddTestData' );
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
	 * Adds text test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding text-list test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'text-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for text list domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['text/list'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['media'] = $this->_getMediaData( $refKeys['media'] );

		$this->_addTextData( $testdata, $refIds );

		$this->_status( 'done' );
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _getMediaData( array $keys )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->_additional, 'Default' );

		$urls = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );
		$result = $mediaManager->searchItems( $search );

		$refIds = array();
		foreach( $result as $item ) {
			$refIds['media/' . $item->getUrl()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the text-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addTextData( array $testdata, array $refIds )
	{
		$textManager = MShop_Text_Manager_Factory::createManager( $this->_additional, 'Default' );
		$textListManager = $textManager->getSubManager( 'list', 'Default' );
		$textListTypeManager = $textListManager->getSubmanager( 'type', 'Default' );

		$labels = array();
		foreach( $testdata['text/list'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$parentIds = array();
		foreach( $textManager->searchItems( $search ) as $item ) {
			$parentIds['text/' . $item->getLabel()] = $item->getId();
		}

		$tListTypeIds = array();
		$tListType = $textListTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['text/list/type'] as $key => $dataset )
		{
			$tListType->setId( null );
			$tListType->setCode( $dataset['code'] );
			$tListType->setDomain( $dataset['domain'] );
			$tListType->setLabel( $dataset['label'] );
			$tListType->setStatus( $dataset['status'] );

			$textListTypeManager->saveItem( $tListType );
			$tListTypeIds[$key] = $tListType->getId();
		}

		$tList = $textListManager->createItem();
		foreach( $testdata['text/list'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No text ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $tListTypeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No text list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$tList->setId( null );
			$tList->setParentId( $parentIds[$dataset['parentid']] );
			$tList->setTypeId( $tListTypeIds[$dataset['typeid']] );
			$tList->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$tList->setDomain( $dataset['domain'] );
			$tList->setDateStart( $dataset['start'] );
			$tList->setDateEnd( $dataset['end'] );
			$tList->setConfig( $dataset['config'] );
			$tList->setPosition( $dataset['pos'] );
			$tList->setStatus( $dataset['status'] );

			$textListManager->saveItem( $tList, false );
		}

		$this->_conn->commit();
	}
}