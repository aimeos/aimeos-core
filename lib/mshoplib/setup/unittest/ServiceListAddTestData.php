<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds service list test data.
 */
class ServiceListAddTestData extends \Aimeos\MW\Setup\Task\Base
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
		return [];
	}


	/**
	 * Adds service test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding service-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'service-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['service/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function getPriceData( array $keys )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Standard' );

		$value = $ship = $domain = $code = [];
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 5 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref price are set wrong "%1$s"', $dataset ) );
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

		$typeids = [];
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

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function getTextData( array $keys )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->additional, 'Standard' );

		$labels = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function getMediaData( array $keys )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->additional, 'Standard' );

		$labels = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.label', $labels ) );

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addServiceListData( array $testdata, array $refIds )
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->additional, 'Standard' );
		$serviceListManager = $serviceManager->getSubManager( 'lists', 'Standard' );

		$typeDomain = $typeCode = $itemCode = [];
		foreach( $testdata['service/lists'] as $dataset )
		{
			$exp = explode( '/', $dataset['parentid'] );

			if( count( $exp ) != 3 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$typeDomain[] = $exp[0];
			$typeCode[] = $exp[1];
			$itemCode[] = $exp[2];
		}


		$this->conn->begin();

		$typeids = $this->getServiceTypeIds( $typeDomain, $typeCode );
		$listItemTypeIds = $this->getServiceListTypeIds( $testdata['service/lists/type'] );
		$parentIds = $this->getServiceIds( $itemCode, $typeids );

		$listItem = $serviceListManager->createItem();
		foreach( $testdata['service/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No service ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No service list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
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


	/**
	 * Returns the service list type IDs for the given data sets
	 *
	 * @param array $data Associative list of identifiers as keys and data sets as values
	 * @return array Associative list of identifiers as keys and list type IDs as values
	 */
	protected function getServiceListTypeIds( array $data )
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->additional, 'Standard' );
		$listManager = $manager->getSubManager( 'lists', 'Standard' );
		$listTypeManager = $listManager->getSubManager( 'type', 'Standard' );

		$listItemTypeIds = [];
		$listItemType = $listTypeManager->createItem();

		foreach( $data as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$listTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		return $listItemTypeIds;
	}


	/**
	 * Returns the service IDs for the given data
	 *
	 * @param array $codes Service codes
	 * @param array $typeIds List of price type IDs
	 * @param array Associative list of identifiers as keys and service IDs as values
	 */
	protected function getServiceIds( array $codes, array $typeIds )
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->additional, 'Standard' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.code', $codes ),
			$search->compare( '==', 'service.typeid', $typeIds ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = [];
		foreach( $manager->searchItems( $search ) as $item ) {
			$parentIds['service/' . $item->getType() . '/' . $item->getCode()] = $item->getId();
		}

		return $parentIds;
	}


	/**
	 * Returns the service type IDs for the given domains and codes
	 *
	 * @param array $domain Domain the service type is for
	 * @param array $code Code the service type is for
	 * @return array List of service type IDs
	 */
	protected function getServiceTypeIds( array $domain, array $code )
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->additional, 'Standard' );
		$typeManager = $manager->getSubManager( 'type', 'Standard' );

		$search = $typeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', $domain ),
			$search->compare( '==', 'service.type.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = [];
		foreach( $typeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		return $typeids;
	}
}