<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds customer list test data.
 */
class CustomerListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'TextAddTestData', 'ProductAddTestData', 'CustomerAddTestData' );
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
	 * Adds customer test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding customer-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'customer-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for customer list domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['customer/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$refIds['product'] = $this->getProductData( $refKeys['product'] );
		$refIds['customer/group'] = $this->getCustomerGroupData( $refKeys['customer/group'] );
		$this->addCustomerListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Returns the required customer group item IDs
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getCustomerGroupData( array $keys )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->additional, 'Standard' );
		$groupManager = $manager->getSubManager( 'group' );

		$codes = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strrpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for referenced customer groups are wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $groupManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.group.code', $codes ) );

		$refIds = [];
		foreach( $groupManager->searchItems( $search ) as $item ) {
			$refIds['customer/group/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required product item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getProductData( array $keys )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );

		$codes = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref products are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		$refIds = [];
		foreach( $manager->searchItems( $search ) as $item ) {
			$refIds['product/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
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
	 * Adds the customer-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @param string $type Manager type string
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addCustomerListData( array $testdata, array $refIds, $type = 'Standard' )
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->additional, $type );
		$customerListManager = $customerManager->getSubManager( 'lists', $type );
		$customerListTypeManager = $customerListManager->getSubmanager( 'type', $type );

		$itemCode = [];
		foreach( $testdata['customer/lists'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$itemCode[] = $str;
		}

		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $itemCode ) );

		$parentIds = [];
		foreach( $customerManager->searchItems( $search ) as $item ) {
			$parentIds['customer/' . $item->getCode()] = $item->getId();
		}

		$listItemTypeIds = [];
		$listItemType = $customerListTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['customer/lists/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$customerListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		$listItem = $customerListManager->createItem();
		foreach( $testdata['customer/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No customer ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%2$s" ref ID found for "%1$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No customer list type ID found for "%1$s"', $dataset['typeid'] ) );
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

			$customerListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}
}