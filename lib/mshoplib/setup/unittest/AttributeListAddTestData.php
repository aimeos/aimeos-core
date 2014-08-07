<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds attribute test data and all items from other domains.
 */
class MW_Setup_Task_AttributeListAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'TextAddTestData', 'MediaAddTestData', 'AttributeAddTestData', 'PriceAddTestData' );
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds attribute-list test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding attribute-list test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'attribute-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for attribute domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['attribute/list'] as $dataset ) {
			$refKeys[ $dataset['domain'] ][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['media'] = $this->_getMediaData( $refKeys['media'] );
		$refIds['price'] = $this->_getPriceData( $refKeys['price'] );
		$refIds['text'] = $this->_getTextData( $refKeys['text'] );

		$this->_addAttributeListData( $testdata, $refIds );

		$this->_status( 'done' );
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List with referenced Ids
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
	 * Gets required text item ids.
	 *
	 * @param array $keys List with referenced Ids
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
	 * Gets required price item ids.
	 *
	 * @param array $keys List with referenced Ids
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _getPriceData( array $keys )
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_additional, 'Default' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Default' );

		$value = $ship = $domain = $code = array();
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 5 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref price are set wrong "%1$s"', $dataset ) );
			}

			$domain[] = $exp[1];
			$code[] = $exp[2];
			$value[] = $exp[3];
			$ship[] = $exp[4];
		}

		$search = $priceTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.type.domain', $domain ),
			$search->compare( '==', 'price.type.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = array();
		foreach( $priceTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $priceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', $value ),
			$search->compare( '==', 'price.costs', $ship ),
			$search->compare( '==', 'price.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = array();
		foreach( $priceManager->searchItems( $search ) as $item )	{
			$refIds[ 'price/'.$item->getDomain().'/'.$item->getType().'/'.$item->getValue().'/'.$item->getCosts() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets the attribute test data and adds attribute-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addAttributeListData( array $testdata, array $refIds )
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_additional, 'Default' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Default' );
		$attributeListManager = $attributeManager->getSubManager( 'list', 'Default' );
		$attributeListTypeManager = $attributeListManager->getSubManager( 'type', 'Default' );

		$codes = $typeCodes = array();
		foreach( $testdata['attribute/list'] as $dataset )
		{
			$exp = explode( '/', $dataset['parentid'] );

			if( count( $exp ) != 3 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$typeCodes[] = $exp[1];
			$codes[] = $exp[2];
		}

		$search = $attributeTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', $typeCodes ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = array();
		foreach( $attributeTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$serch = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $codes ),
			$search->compare( '==', 'attribute.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = array();
		foreach( $attributeManager->searchItems( $search ) as $item )	{
			$parentIds[ 'attribute/'.$item->getType().'/'.$item->getCode() ] = $item->getId();
		}

		$listItemTypeIds = array();
		$listItemType = $attributeListTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['attribute/list/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$attributeListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[ $key ] = $listItemType->getId();
		}

		$listItem = $attributeListManager->createItem();
		foreach( $testdata['attribute/list'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No attribute ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No attribute list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[ $dataset['domain'] ][ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[ $dataset['parentid'] ] );
			$listItem->setTypeId( $listItemTypeIds[ $dataset['typeid'] ] );
			$listItem->setRefId( $refIds[ $dataset['domain'] ][ $dataset['refid'] ] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$attributeListManager->saveItem( $listItem, false );
		}

		$this->_conn->commit();
	}
}