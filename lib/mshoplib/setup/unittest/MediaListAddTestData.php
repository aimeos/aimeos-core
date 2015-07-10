<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds media test data.
 */
class MW_Setup_Task_MediaListAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'AttributeListAddTestData' );
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
	 * Adds media test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg('Adding media-list test data', 0);
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'media-list.php';

		if( ( $testdata = include( $path ) ) == false ){
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for media list domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['media/list'] as $dataset ) {
			$refKeys[ $dataset['domain'] ][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['text'] = $this->_getTextData( $refKeys['text'] );
		$refIds['attribute'] = $this->_getAttributeData( $refKeys['attribute'] );

		$this->_addMediaListData( $testdata, $refIds );

		$this->_status( 'done' );
	}


	/**
	 * Gets required attribute item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _getAttributeData( array $keys )
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_additional, 'Default' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Default' );

		$codes = $typeCodes = $domains = array();
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 4 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref attribute are set wrong "%1$s"', $dataset ) );
			}

			$domains[] = $exp[1];
			$typeCodes[] = $exp[2];
			$codes[] = $exp[3];
		}

		$search = $attributeTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', $domains ),
			$search->compare( '==', 'attribute.type.code', $typeCodes ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $attributeTypeManager->searchItems( $search );

		$typeids = array();
		foreach( $result as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $codes ),
			$search->compare( '==', 'attribute.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = array();
		foreach( $attributeManager->searchItems( $search ) as $item )	{
			$refIds[ 'attribute/'.$item->getDomain().'/'.$item->getType().'/'.$item->getCode() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _getTextData( array $keys )
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
	 * Adds the media-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addMediaListData( array $testdata, array $refIds )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->_additional, 'Default' );
		$mediaListManager = $mediaManager->getSubmanager( 'list', 'Default' );
		$mediaListTypeManager = $mediaListManager->getSubManager( 'type', 'Default' );

		$urls = array();
		foreach( $testdata['media/list'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos+1 ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$result = $mediaManager->searchItems( $search );

		$parentIds = array();
		foreach( $result as $item )	{
			$parentIds[ 'media/'.$item->getUrl() ] = $item->getId();
		}

		$medListTypes = array();
		$medListType = $mediaListTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['media/list/type'] as $key => $dataset )
		{
			$medListType->setId( null );
			$medListType->setCode( $dataset['code'] );
			$medListType->setDomain( $dataset['domain'] );
			$medListType->setLabel( $dataset['label'] );
			$medListType->setStatus( $dataset['status'] );

			$mediaListTypeManager->saveItem( $medListType );
			$medListTypes[ $key ] = $medListType->getId();
		}

		$medList = $mediaListManager->createItem();
		foreach( $testdata['media/list'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No media ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $medListTypes[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No media list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[ $dataset['domain'] ][ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$medList->setId( null );
			$medList->setParentId( $parentIds[ $dataset['parentid'] ] );
			$medList->setTypeId( $medListTypes[ $dataset['typeid'] ] );
			$medList->setRefId( $refIds[ $dataset['domain'] ] [ $dataset['refid'] ] );
			$medList->setDomain( $dataset['domain'] );
			$medList->setDateStart( $dataset['start'] );
			$medList->setDateEnd( $dataset['end'] );
			$medList->setConfig( $dataset['config'] );
			$medList->setPosition( $dataset['pos'] );
			$medList->setStatus( $dataset['status'] );

			$mediaListManager->saveItem( $medList, false );
		}

		$this->_conn->commit();
	}
}