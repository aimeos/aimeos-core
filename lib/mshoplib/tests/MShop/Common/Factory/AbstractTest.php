<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Common_Factory_Abstract.
 */
class MShop_Common_Factory_AbstractTest extends MW_Unittest_Testcase
{
	private $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Common_Factory_AbstractTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->_context, 'Default' );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', $manager );

		$injectedManager = MShop_Attribute_Manager_Factory::createManager( $this->_context, 'DefaultMock' );

		$this->assertSame( $manager, $injectedManager );
	}


	public function testInjectManagerReset()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->_context, 'Default' );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', $manager );
		MShop_Attribute_Manager_Factory::injectManager( 'MShop_Attribute_Manager_DefaultMock', null );

		$this->setExpectedException( 'MShop_Exception' );
		MShop_Attribute_Manager_Factory::createManager( $this->_context, 'DefaultMock' );
	}

}