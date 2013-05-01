<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */


class Controller_ExtJS_Plugin_Decorator_ExampleTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Plugin_Decorator_ExampleTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$controller = Controller_ExtJS_Plugin_Factory::createController( $context );
		$this->_object = new Controller_ExtJS_Plugin_Decorator_Example( $context, $controller );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'plugin.provider' => 'Shipping' ) ) ) ),


			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		if( ( $plugin = reset( $result ) ) === false ) {
			throw new Exception( 'No plugin found' );
		}

		$this->assertEquals( 1, count( $plugin ) );
		$this->assertEquals( reset( $plugin )->{'plugin.provider'}, 'Shipping,Example');
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'plugin.type.code', 'order' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No plugin type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'plugin.provider' => 'test provider',
				'plugin.typeid' => $type->getId(),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'plugin.provider' => 'test provider' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'plugin.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->_object->getServiceDescription();

		$this->assertArrayHasKey( 'Plugin.init', $actual );
	}


	public function testGetSearchSchema()
	{
		$actual = $this->_object->getSearchSchema();

		$this->assertArrayHasKey( 'criteria', $actual );
	}


	public function testGetItemSchema()
	{
		$actual = $this->_object->getItemSchema();

		$this->assertArrayHasKey( 'name', $actual );
		$this->assertArrayHasKey( 'properties', $actual );
	}


	public function testCall()
	{
		$result = $this->_object->__call('getServiceDescription', array() );
		$this->assertInternalType('array', $result);
	}

}
