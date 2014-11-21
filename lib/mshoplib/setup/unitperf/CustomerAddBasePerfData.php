<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to customer table.
 */
class MW_Setup_Task_CustomerAddBasePerfData extends MW_Setup_Task_ProductAddBasePerfData
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
		return array();
	}


	/**
	 * Inserts customer items.
	 */
	protected function _process()
	{
		$this->_msg( 'Adding customer performance data', 0 );


		$context =  $this->_getContext();
		$customerManager = MShop_Customer_Manager_Factory::createManager( $context );

		$customerItem = $customerManager->createItem();
		$customerItem->setCode( 'demo-test' );
		$customerItem->setLabel( 'Test demo unitperf user' );
		$customerItem->setPassword( sha1( microtime(true) . getmypid() . rand() ) );
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

		$this->_status( 'done' );
	}
}