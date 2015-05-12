<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds demo records to customer tables.
 */
class MW_Setup_Task_DemoAddCustomerData extends MW_Setup_Task_MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataDefault' );
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
	 * Insert service data.
	 */
	protected function _process()
	{
		$this->_msg( 'Processing customer demo data', 0 );

		$context =  $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'customer' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'customer.code', 'demo-' ) );
		$services = $manager->searchItems( $search );

		$manager->deleteItems( array_keys( $services ) );


		if( $context->getConfig()->get( 'setup/default/demo', false ) == true )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-customer.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new MShop_Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
			}

			$this->_saveCustomerItems( $data );

			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}


	/**
	 * Stores the customer items
	 *
	 * @param array $data List of arrays containing the customer properties
	 */
	protected function _saveCustomerItems( array $data )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'customer' );

		foreach( $data as $entry )
		{
			$item = $manager->createItem();
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
			$item->setBirthday( $entry['birthday'] );
			$item->setPassword( $entry['password'] );
			$item->setStatus( $entry['status'] );
			$item->setDateVerified( $entry['vtime'] );

			$addr = $item->getPaymentAddress();
			$addr->setTitle( $entry['title'] );
			$addr->setSalutation( $entry['salutation'] );
			$addr->setCompany( $entry['company'] );
			$addr->setVatID( $entry['vatid'] );
			$addr->setFirstname( $entry['firstname'] );
			$addr->setLastname( $entry['lastname'] );
			$addr->setAddress1( $entry['address1'] );
			$addr->setAddress2( $entry['address2'] );
			$addr->setAddress3( $entry['address3'] );
			$addr->setPostal( $entry['postal'] );
			$addr->setCity( $entry['city'] );
			$addr->setState( $entry['state'] );
			$addr->setLanguageId( $entry['langid'] );
			$addr->setCountryId( $entry['countryid'] );
			$addr->setTelephone( $entry['telephone'] );
			$addr->setEmail( $entry['email'] );
			$addr->setTelefax( $entry['telefax'] );
			$addr->setWebsite( $entry['website'] );

			$manager->saveItem( $item );

			if( isset( $entry['delivery'] ) ) {
				$this->_saveAddressItems( $entry['delivery'], $item->getId() );
			}
		}
	}


	/**
	 * Stores the customer items
	 *
	 * @param array $data List of arrays containing the customer properties
	 * @param string $id Unique ID of the customer item
	 */
	protected function _saveAddressItems( array $data, $id )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'customer/address' );

		foreach( $data as $entry )
		{
			$addr = $manager->createItem();
			$addr->setRefId( $id );
			$addr->setTitle( $entry['title'] );
			$addr->setSalutation( $entry['salutation'] );
			$addr->setCompany( $entry['company'] );
			$addr->setVatID( $entry['vatid'] );
			$addr->setFirstname( $entry['firstname'] );
			$addr->setLastname( $entry['lastname'] );
			$addr->setAddress1( $entry['address1'] );
			$addr->setAddress2( $entry['address2'] );
			$addr->setAddress3( $entry['address3'] );
			$addr->setPostal( $entry['postal'] );
			$addr->setCity( $entry['city'] );
			$addr->setState( $entry['state'] );
			$addr->setLanguageId( $entry['langid'] );
			$addr->setCountryId( $entry['countryid'] );
			$addr->setTelephone( $entry['telephone'] );
			$addr->setEmail( $entry['email'] );
			$addr->setTelefax( $entry['telefax'] );
			$addr->setWebsite( $entry['website'] );

			$manager->saveItem( $addr );
		}
	}
}