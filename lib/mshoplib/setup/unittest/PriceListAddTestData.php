<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds price-list test data and all items from other domains.
 */
class PriceListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'CustomerListAddTestData', 'PriceAddTestData' );
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
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds price test data.
	 */
	protected function process()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding price-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'price-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for price domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['price/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['customer'] = $this->getCustomerData( $refKeys['customer'] );

		$this->addPriceListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required customer item ids.
	 *
	 * @param array $keys List with referenced Ids
	 * @return array $refIds List with referenced Ids
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function getCustomerData( array $keys )
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->additional, 'Standard' );

		$codes = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref customer are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $codes ) );

		$refIds = array();
		foreach( $customerManager->searchItems( $search ) as $item ) {
			$refIds['customer/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the price-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addPriceListData( array $testdata, array $refIds )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Standard' );
		$priceListManager = $priceManager->getSubManager( 'lists', 'Standard' );
		$priceListTypeManager = $priceListManager->getSubManager( 'type', 'Standard' );

		$value = $ship = $domain = $code = array();
		foreach( $testdata['price/lists'] as $dataset )
		{
			$exp = explode( '/', $dataset['parentid'] );

			if( count( $exp ) != 5 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
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
			$search->compare( '==', 'price.typeid', $typeids )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = array();
		foreach( $priceManager->searchItems( $search ) as $item ) {
			$parentIds['price/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getValue() . '/' . $item->getCosts()] = $item->getId();
		}

		$listItemTypeIds = array();
		$listItemType = $priceListTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['price/lists/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$priceListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		$listItem = $priceListManager->createItem();
		foreach( $testdata['price/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No price ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No attribute list type ID found for "%1$s"', $dataset['typeid'] ) );
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

			$priceListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}
}