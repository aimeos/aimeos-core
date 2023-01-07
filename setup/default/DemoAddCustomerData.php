<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to customer tables.
 */
class DemoAddCustomerData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Customer', 'MShopSetLocale', 'MShopAddTypeDataDefault', 'MShopAddCodeDataDefault'];
	}


	/**
	 * Insert service data.
	 */
	public function up()
	{
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing customer demo data', 'vv' );

		$manager = \Aimeos\MShop::create( $context, 'customer' );
		$search = $manager->filter()->add( ['customer.code' => 'demo@example.com'] );
		$manager->delete( $manager->search( $search ) );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-customer.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \RuntimeException( sprintf( 'No file "%1$s" found for customer domain', $path ) );
			}

			$this->saveCustomerItems( $data );
		}
	}


	/**
	 * Stores the customer items
	 *
	 * @param array $data List of arrays containing the customer properties
	 */
	protected function saveCustomerItems( array $data )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'customer' );

		foreach( $data as $entry )
		{
			$item = $manager->create();
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
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
			$addr->setBirthday( $entry['birthday'] );

			$manager->save( $item );

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
		$manager = \Aimeos\MShop::create( $this->context(), 'customer/address' );

		foreach( $data as $entry )
		{
			$addr = $manager->create();
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
			$addr->setBirthday( $entry['birthday'] );

			$manager->save( $addr );
		}
	}
}
