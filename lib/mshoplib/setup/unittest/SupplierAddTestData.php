<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds supplier test data and all items from other domains.
 */
class SupplierAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductListAddTestData' );
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
	 * Adds supplier test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding supplier test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addSupplierData();

		$this->status( 'done' );
	}


	/**
	 * Adds the supplier test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addSupplierData()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( $this->additional, 'Standard' );
		$supplierAddressManager = $supplierManager->getSubManager( 'address', 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'supplier.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
		}

		$supIds = [];
		$supplier = $supplierManager->createItem();

		$this->conn->begin();

		foreach( $testdata['supplier'] as $key => $dataset )
		{
			$supplier->setId( null );
			$supplier->setCode( $dataset['code'] );
			$supplier->setLabel( $dataset['label'] );
			$supplier->setStatus( $dataset['status'] );

			$supplierManager->saveItem( $supplier );
			$supIds[$key] = $supplier->getId();
		}

		$supAdr = $supplierAddressManager->createItem();
		foreach( $testdata['supplier/address'] as $dataset )
		{
			if( !isset( $supIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No supplier ID found for "%1$s"', $dataset['refid'] ) );
			}

			$supAdr->setId( null );
			$supAdr->setCompany( $dataset['company'] );
			$supAdr->setVatID( ( isset( $dataset['vatid'] ) ? $dataset['vatid'] : '' ) );
			$supAdr->setVatID( $dataset['vatid'] );
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
			$supAdr->setLatitude( $dataset['latitude'] );
			$supAdr->setLongitude( $dataset['longitude'] );
			$supAdr->setParentId( $supIds[$dataset['parentid']] );

			$supplierAddressManager->saveItem( $supAdr, false );
		}

		$this->conn->commit();
	}
}