<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds service list test data.
 */
class MW_Setup_Task_ServiceListAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'CatalogListAddTestData', 'ServiceAddTestData' );
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
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds service test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding service-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'service-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['service/list'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['media'] = $this->getMediaData( $refKeys['media'] );
		$refIds['price'] = $this->getPriceData( $refKeys['price'] );
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$this->addServiceListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required price item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function getPriceData( array $keys )
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->additional, 'Default' );
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

		$result = $priceTypeManager->searchItems( $search );

		$typeids = array();
		foreach( $result as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $priceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', $value ),
			$search->compare( '==', 'price.costs', $ship ),
			$search->compare( '==', 'price.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $priceManager->searchItems( $search );

		$refIds = array();
		foreach( $result as $item ) {
			$refIds['price/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getValue() . '/' . $item->getCosts()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function getTextData( array $keys )
	{
		$textManager = MShop_Text_Manager_Factory::createManager( $this->additional, 'Default' );

		$labels = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = array();
		foreach( $textManager->searchItems( $search ) as $item ) {
			$refIds['text/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function getMediaData( array $keys )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->additional, 'Default' );

		$labels = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.label', $labels ) );

		$refIds = array();
		foreach( $mediaManager->searchItems( $search ) as $item ) {
			$refIds['media/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the service-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function addServiceListData( array $testdata, array $refIds )
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->additional, 'Default' );
		$serviceTypeManager = $serviceManager->getSubManager( 'type', 'Default' );
		$serviceListManager = $serviceManager->getSubManager( 'list', 'Default' );
		$serviceListTypeManager = $serviceListManager->getSubmanager( 'type', 'Default' );

		$typeDomain = $typeCode = $itemCode = array();
		foreach( $testdata['service/list'] as $dataset )
		{
			$exp = explode( '/', $dataset['parentid'] );

			if( count( $exp ) != 3 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$typeDomain[] = $exp[0];
			$typeCode[] = $exp[1];
			$itemCode[] = $exp[2];
		}

		$search = $serviceTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', $typeDomain ),
			$search->compare( '==', 'service.type.code', $typeCode ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = array();
		foreach( $serviceTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $serviceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.code', $itemCode ),
			$search->compare( '==', 'service.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = array();
		foreach( $serviceManager->searchItems( $search ) as $item ) {
			$parentIds['service/' . $item->getType() . '/' . $item->getCode()] = $item->getId();
		}

		$listItemTypeIds = array();
		$listItemType = $serviceListTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['service/list/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$serviceListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		$listItem = $serviceListManager->createItem();
		foreach( $testdata['service/list'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No service ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No service list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[$dataset['parentid']] );
			$listItem->setTypeId( $listItemTypeIds[$dataset['typeid']] );
			$listItem->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$serviceListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}
}