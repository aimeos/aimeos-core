<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Common_Factory_Base.
 */
class MShop_Common_Factory_BaseTest extends PHPUnit_Framework_TestCase
{
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'mshop/common/manager/decorators/default', array() );
		$config->set( 'mshop/attribute/manager/decorators/global', array() );
		$config->set( 'mshop/attribute/manager/decorators/local', array() );
	}


	protected function tearDown()
	{
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', null );
	}


	public function testInjectManager()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->context, 'Default' );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', $manager );

		$injectedManager = MShop_Attribute_Manager_Factory::createManager( $this->context, 'DefaultMock' );

		$this->assertSame( $manager, $injectedManager );
	}


	public function testInjectManagerReset()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->context, 'Default' );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', $manager );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', null );

		$this->setExpectedException( 'MShop_Exception' );
		MShop_Attribute_Manager_Factory::createManager( $this->context, 'DefaultMock' );
	}

}