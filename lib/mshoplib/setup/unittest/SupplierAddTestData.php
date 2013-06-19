<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds supplier test data and all items from other domains.
 */
class MW_Setup_Task_SupplierAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'ProductListAddTestData' );
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
	 * Adds supplier test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding supplier test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$this->_addSupplierData();

		$this->_status( 'done' );
	}


	/**
	 * Adds the supplier test data.
	 *
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addSupplierData()
	{
		$supplierManager = MShop_Supplier_Manager_Factory::createManager( $this->_additional, 'Default' );
		$supplierAddressManager = $supplierManager->getSubManager( 'address', 'Default' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'supplier.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
		}

		$supIds = array();
		$supplier = $supplierManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['supplier'] as $key => $dataset )
		{
			$supplier->setId( null );
			$supplier->setCode( $dataset['code'] );
			$supplier->setLabel( $dataset['label'] );
			$supplier->setStatus( $dataset['status'] );

			$supplierManager->saveItem( $supplier );
			$supIds[ $key ] = $supplier->getId();
		}

		$supAdr = $supplierAddressManager->createItem();
		foreach( $testdata['supplier/address'] as $dataset )
		{
			if( !isset( $supIds[ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No supplier ID found for "%1$s"', $dataset['refid'] ) );
			}

			$supAdr->setId( null );
			$supAdr->setCompany( $dataset['company'] );
			$supAdr->setSalutation( $dataset['salutation'] );
			$supAdr->setTitle( $dataset['title'] );
			$supAdr->setFirstname( $dataset['firstname'] );
			$supAdr->setLastname( $dataset['lastname'] );
			$supAdr->setAddress1( $dataset['address1'] );
			$supAdr->setAddress2( $dataset['address2'] );
			$supAdr->setAddress3( $dataset['address3'] );
			$supAdr->setPostal( $dataset['postal'] );
			$supAdr->setCity( $dataset['city'] );
			$supAdr->setState( $dataset['state'] );
			$supAdr->setCountryId( $dataset['countryid'] );
			$supAdr->setTelephone( $dataset['telephone'] );
			$supAdr->setEmail( $dataset['email'] );
			$supAdr->setTelefax( $dataset['telefax'] );
			$supAdr->setWebsite( $dataset['website'] );
			$supAdr->setLanguageId( $dataset['langid'] );
			$supAdr->setRefId( $supIds[ $dataset['refid'] ] );

			$supplierAddressManager->saveItem( $supAdr, false );
		}

		$this->_conn->commit();
	}
}