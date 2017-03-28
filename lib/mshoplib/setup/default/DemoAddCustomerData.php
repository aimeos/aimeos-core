<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to customer tables.
 */
class DemoAddCustomerData extends \Aimeos\MW\Setup\Task\MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataDefault', 'MShopAddCodeDataDefault' );
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
	 * Insert service data.
	 */
	public function migrate()
	{
		$this->msg( 'Processing customer demo data', 0 );

		$context = $this->getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->status( 'OK' );
			return;
		}


		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', 'customer.code', 'demo-' ) );
		$services = $manager->searchItems( $search );

		$manager->deleteItems( array_keys( $services ) );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-customer.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for customer domain', $path ) );
			}

			$this->saveCustomerItems( $data );

			$this->status( 'added' );
		}
		else
		{
			$this->status( 'removed' );
		}
	}


	/**
	 * Stores the customer items
	 *
	 * @param array $data List of arrays containing the customer properties
	 */
	protected function saveCustomerItems( array $data )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer' );

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
				$this->saveAddressItems( $entry['delivery'], $item->getId() );
			}
		}
	}


	/**
	 * Stores the customer items
	 *
	 * @param array $data List of arrays containing the customer properties
	 * @param string $id Unique ID of the customer item
	 */
	protected function saveAddressItems( array $data, $id )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer/address' );

		foreach( $data as $entry )
		{
			$addr = $manager->createItem();
			$addr->setParentId( $id );
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