<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds performance records to customer table.
 */
class CustomerAddPerfData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData', 'MShopSetLocale'];
	}


	/**
	 * Inserts customer items.
	 */
	public function up()
	{
		$this->info( 'Adding customer performance data', 'v' );

		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context() );

		$customerItem = $customerManager->create();
		$customerItem->setCode( 'unitperf@example.com' );
		$customerItem->setLabel( 'Test demo unitperf user' );
		$customerItem->setPassword( sha1( microtime( true ) . getmypid() . rand() ) );
		$customerItem->setStatus( 1 );

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setCompany( 'Test company' );
		$addrItem->setVatID( 'DE999999999' );
		$addrItem->setSalutation( 'mr' );
		$addrItem->setFirstname( 'Testdemo' );
		$addrItem->setLastname( 'Perfuser' );
		$addrItem->setAddress1( 'Test street' );
		$addrItem->setAddress2( '1' );
		$addrItem->setPostal( '1000' );
		$addrItem->setCity( 'Test city' );
		$addrItem->setLanguageId( 'en' );
		$addrItem->setCountryId( 'DE' );
		$addrItem->setEmail( 'unitperf@example.com' );

		$customerManager->save( $customerItem );
	}
}
