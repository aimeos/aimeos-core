<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds customer test data.
 */
class MW_Setup_Task_CustomerAddTestData extends MW_Setup_Task_Base
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
	 * Adds customer test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding customer test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'customer.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
		}

		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->additional, 'Standard' );
		$customerAddressManager = $customerManager->getSubManager( 'address', 'Standard' );
		$customerGroupManager = $customerManager->getSubManager( 'group', 'Standard' );

		$this->conn->begin();

		$parentIds = $this->addCustomerData( $testdata, $customerManager, $customerAddressManager->createItem() );
		$this->addCustomerAddressData( $testdata, $customerAddressManager, $parentIds );
		$this->addCustomerGroupData( $testdata, $customerGroupManager, $parentIds );

		$this->conn->commit();

		$this->status( 'done' );

	}


	/**
	 * Adds the customer test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param MShop_Common_Manager_Iface $customerManager Customer manager
	 * @param MShop_Common_Item_Address_Iface $address Customer address item
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function addCustomerData( array $testdata, MShop_Common_Manager_Iface $customerManager,
		MShop_Common_Item_Address_Iface $address )
	{
		$parentIds = array();
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
	 * @param MShop_Common_Manager_Iface $customerAddressManager Customer address manager
	 * @param array $parentIds Associative list of keys of the customer test data and customer IDs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function addCustomerAddressData( array $testdata, MShop_Common_Manager_Iface $customerAddressManager,
		array $parentIds )
	{
		$address = $customerAddressManager->createItem();

		foreach( $testdata['customer/address'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['refid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No customer ID found for "%1$s"', $dataset['refid'] ) );
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
			$address->setFlag( $dataset['flag'] );
			$address->setPosition( $dataset['pos'] );
			$address->setRefId( $parentIds[$dataset['refid']] );

			$customerAddressManager->saveItem( $address, false );
		}
	}


	/**
	 * Adds the customer group test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param MShop_Common_Manager_Iface $customerGroupManager Customer group manager
	 * @param array $parentIds Associative list of keys of the customer test data and customer IDs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function addCustomerGroupData( array $testdata, MShop_Common_Manager_Iface $customerGroupManager,
		array $parentIds )
	{
		$group = $customerGroupManager->createItem();

		foreach( $testdata['customer/group'] as $dataset )
		{
			$group->setId( null );
			$group->setCode( $dataset['code'] );
			$group->setLabel( $dataset['label'] );

			$customerGroupManager->saveItem( $group, false );
		}
	}
}