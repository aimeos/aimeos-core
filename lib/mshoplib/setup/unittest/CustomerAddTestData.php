<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds customer test data.
 */
class MW_Setup_Task_CustomerAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'MediaAddTestData' );
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
	 * Adds customer test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding customer test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'customer.php';

		if( ( $testdata = include( $path ) ) == false ){
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
		}

		$this->_addCustomerData( $testdata );

		$this->_status( 'done' );

	}


	/**
	 * Adds the customer test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addCustomerData( array $testdata )
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_additional, 'Default' );
		$customerAddressManager = $customerManager->getSubManager( 'address', 'Default' );

		$parentIds = array();
		$customer = $customerManager->createItem();
		$address = $customerAddressManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['customer'] as $key => $dataset )
		{
			$address->setCompany( $dataset['company'] );
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
			$customer->setBillingAddress( $address );

			$customerManager->saveItem( $customer );
			$parentIds[ $key ] = $customer->getId();
		}


		foreach ( $testdata['customer/address'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No customer ID found for "%1$s"', $dataset['refid'] ) );
			}

			$address->setId(null);
			$address->setCompany( $dataset['company'] );
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
			$address->setRefId( $parentIds[ $dataset['refid'] ] );

			$customerAddressManager->saveItem( $address, false );
		}

		$this->_conn->commit();
	}
}