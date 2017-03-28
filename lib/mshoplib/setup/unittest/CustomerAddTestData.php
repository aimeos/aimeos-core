<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds customer test data.
 */
class CustomerAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaAddTestData' );
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
	 * Adds customer test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding customer test data', 0 );

		$ds = DIRECTORY_SEPARATOR;
		$this->additional->setEditor( 'core:unittest' );

		$this->process( __DIR__ . $ds . 'data' . $ds . 'customer.php' );

		$this->status( 'done' );

	}


	/**
	 * Adds the customer data
	 *
	 * @param string $path Path to data file
	 * @throws \Aimeos\MShop\Exception
	 */
	protected function process( $path )
	{
		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
		}

		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->additional, 'Standard' );
		$customerAddressManager = $customerManager->getSubManager( 'address', 'Standard' );
		$customerGroupManager = $customerManager->getSubManager( 'group', 'Standard' );

		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '=~', 'customer.code', 'UTC00' ) );
		$items = $customerManager->searchItems( $search );

		$this->conn->begin();

		$customerManager->deleteItems( array_keys( $items ) );
		$parentIds = $this->addCustomerData( $testdata, $customerManager, $customerAddressManager->createItem() );
		$this->addCustomerAddressData( $testdata, $customerAddressManager, $parentIds );
		$this->addCustomerGroupData( $testdata, $customerGroupManager );

		$this->conn->commit();
	}


	/**
	 * Adds the customer test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Manager\Iface $customerManager Customer manager
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Customer address item
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addCustomerData( array $testdata, \Aimeos\MShop\Common\Manager\Iface $customerManager,
		\Aimeos\MShop\Common\Item\Address\Iface $address )
	{
		$parentIds = [];
		$customer = $customerManager->createItem();

		foreach( $testdata['customer'] as $key => $dataset )
		{
			$address->setCompany( $dataset['company'] );
			$address->setVatID( ( isset( $dataset['vatid'] ) ? $dataset['vatid'] : '' ) );
			$address->setSalutation( $dataset['salutation'] );
			$address->setTitle( $dataset['title'] );
			$address->setFirstname( $dataset['firstname'] );
			$address->setLastname( $dataset['lastname'] );
			$address->setAddress1( $dataset['address1'] );
			$address->setAddress2( $dataset['address2'] );
			$address->setAddress3( $dataset['address3'] );
			$address->setPostal( $dataset['postal'] );
			$address->setCity( $dataset['city'] );
			$address->setState( $dataset['state'] );
			$address->setCountryId( $dataset['countryid'] );
			$address->setTelephone( $dataset['telephone'] );
			$address->setEmail( $dataset['email'] );
			$address->setTelefax( $dataset['telefax'] );
			$address->setWebsite( $dataset['website'] );
			$address->setLanguageId( $dataset['langid'] );
			$address->setLatitude( $dataset['latitude'] );
			$address->setLongitude( $dataset['longitude'] );

			$customer->setId( null );
			$customer->setLabel( $dataset['label'] );
			$customer->setCode( $dataset['code'] );
			$customer->setStatus( $dataset['status'] );
			$customer->setPaymentAddress( $address );
			$customer->setPassword( ( isset( $dataset['password'] ) ? $dataset['password'] : '' ) );
			$customer->setBirthday( ( isset( $dataset['birthday'] ) ? $dataset['birthday'] : null ) );

			$customerManager->saveItem( $customer );
			$parentIds[$key] = $customer->getId();
		}

		return $parentIds;
	}


	/**
	 * Adds the customer address test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Manager\Iface $customerAddressManager Customer address manager
	 * @param array $parentIds Associative list of keys of the customer test data and customer IDs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addCustomerAddressData( array $testdata, \Aimeos\MShop\Common\Manager\Iface $customerAddressManager,
		array $parentIds )
	{
		$address = $customerAddressManager->createItem();

		foreach( $testdata['customer/address'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No customer ID found for "%1$s"', $dataset['parentid'] ) );
			}

			$address->setId( null );
			$address->setCompany( $dataset['company'] );
			$address->setVatID( ( isset( $dataset['vatid'] ) ? $dataset['vatid'] : '' ) );
			$address->setSalutation( $dataset['salutation'] );
			$address->setTitle( $dataset['title'] );
			$address->setFirstname( $dataset['firstname'] );
			$address->setLastname( $dataset['lastname'] );
			$address->setAddress1( $dataset['address1'] );
			$address->setAddress2( $dataset['address2'] );
			$address->setAddress3( $dataset['address3'] );
			$address->setPostal( $dataset['postal'] );
			$address->setCity( $dataset['city'] );
			$address->setState( $dataset['state'] );
			$address->setCountryId( $dataset['countryid'] );
			$address->setTelephone( $dataset['telephone'] );
			$address->setEmail( $dataset['email'] );
			$address->setTelefax( $dataset['telefax'] );
			$address->setWebsite( $dataset['website'] );
			$address->setLanguageId( $dataset['langid'] );
			$address->setLatitude( $dataset['latitude'] );
			$address->setLongitude( $dataset['longitude'] );
			$address->setFlag( $dataset['flag'] );
			$address->setPosition( $dataset['pos'] );
			$address->setParentId( $parentIds[$dataset['parentid']] );

			$customerAddressManager->saveItem( $address, false );
		}
	}


	/**
	 * Adds the customer group test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Manager\Iface $customerGroupManager Customer group manager
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	protected function addCustomerGroupData( array $testdata, \Aimeos\MShop\Common\Manager\Iface $customerGroupManager )
	{
		$group = $customerGroupManager->createItem();

		foreach( $testdata['customer/group'] as $dataset )
		{
			$group->setId( null );
			$group->setCode( $dataset['code'] );
			$group->setLabel( $dataset['label'] );

			try {
				$customerGroupManager->saveItem( $group, false );
			} catch( \Exception $e ) { ; } // ignore duplicates
		}
	}
}