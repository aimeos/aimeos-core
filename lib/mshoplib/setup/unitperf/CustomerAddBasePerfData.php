<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to customer table.
 */
class CustomerAddBasePerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeDataUnitperf' );
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
	 * Inserts customer items.
	 */
	public function migrate()
	{
		$this->msg( 'Adding customer performance data', 0 );


		$context = $this->getContext();
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $context );

		$customerItem = $customerManager->createItem();
		$customerItem->setCode( 'demo-test' );
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
		$addrItem->setEmail( 'me@localhost' );

		$customerManager->saveItem( $customerItem );

		$this->status( 'done' );
	}
}