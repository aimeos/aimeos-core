<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
		return ['ServiceAddTestData', 'MediaAddTestData', 'PriceAddTestData', 'TextAddTestData'];
	}


	/**
	 * Adds service test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

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
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $this->additional, 'Standard' );
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

		$typeCodes = [];
		foreach( $result as $item ) {
			$typeCodes[] = $item->getCode();
		}

		$search = $priceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', $value ),
			$search->compare( '==', 'price.costs', $ship ),
			$search->compare( '==', 'price.type', $typeCodes ),
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
		$textManager = \Aimeos\MShop\Text\Manager\Factory::create( $this->additional, 'Standard' );

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
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::create( $this->additional, 'Standard' );

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
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->additional, 'Standard' );
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


		$serviceManager->begin();

		$this->addServiceListTypeItems( $testdata['service/lists/type'] );
		$parentIds = $this->getServiceIds( $itemCode, $typeCode );

		$listItem = $serviceListManager->createItem();
		foreach( $testdata['service/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No service ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[$dataset['parentid']] );
			$listItem->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$listItem->setType( $dataset['type'] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$serviceListManager->saveItem( $listItem, false );
		}

		$serviceManager->commit();
	}


	/**
	 * Adds the service list type items
	 *
	 * @param array $data Associative list of identifiers as keys and data sets as values
	 */
	protected function addServiceListTypeItems( array $data )
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->additional, 'Standard' );
		$listManager = $manager->getSubManager( 'lists', 'Standard' );
		$listTypeManager = $listManager->getSubManager( 'type', 'Standard' );

		$listItemType = $listTypeManager->createItem();

		foreach( $data as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$listTypeManager->saveItem( $listItemType );
		}
	}


	/**
	 * Returns the service IDs for the given data
	 *
	 * @param array $codes Service codes
	 * @param array $typeCodes List of price type codes
	 * @return array Associative list of identifiers as keys and service IDs as values
	 */
	protected function getServiceIds( array $codes, array $typeCodes )
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->additional, 'Standard' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.code', $codes ),
			$search->compare( '==', 'service.type', $typeCodes ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = [];
		foreach( $manager->searchItems( $search ) as $item ) {
			$parentIds['service/' . $item->getType() . '/' . $item->getCode()] = $item->getId();
		}

		return $parentIds;
	}
}
