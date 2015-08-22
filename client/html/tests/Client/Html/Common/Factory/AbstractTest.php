<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Client_Html_Common_Factory_AbstractTest.
 */
class Client_Html_Common_Factory_AbstractTest extends MW_Unittest_Testcase
{
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$config = $this->_context->getConfig();

		$config->set( 'client/html/common/decorators/default', array() );
		$config->set( 'client/html/admin/decorators/global', array() );
		$config->set( 'client/html/admin/decorators/local', array() );
	}


	public function testInjectClient()
	{
		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array(), 'Default' );
		Client_Html_Catalog_Filter_Factory::injectClient( 'Client_Html_Catalog_Filter_Default', $client );

		$injectedClient = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array(), 'Default' );

		$this->assertSame( $client, $injectedClient );
	}


	public function testInjectClientReset()
	{
		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array(), 'Default' );
		Client_Html_Catalog_Filter_Factory::injectClient( 'Client_Html_Catalog_Filter_Default', $client );
		Client_Html_Catalog_Filter_Factory::injectClient( 'Client_Html_Catalog_Filter_Default', null );

		$new = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array(), 'Default' );

		$this->assertNotSame( $client, $new );
	}

}