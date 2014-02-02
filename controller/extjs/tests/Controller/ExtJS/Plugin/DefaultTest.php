<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Plugin_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Plugin_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Plugin_Default( TestHelper::getContext() );
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
		MShop_Factory::clear();
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'plugin.provider' => 'Shipping' ) ) ) ),
			'sort' => 'plugin.provider',
			'dir' => 'ASC',
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
				'plugin.status' => 1,
				'plugin.position' => 2,
				'plugin.provider' => 'test provider',
				'plugin.config' => array( 'url' => 'www.url.de' ),
				'plugin.typeid' => $type->getId(),
				'plugin.label' => 'test plugin',
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
		$this->assertNotNull( $saved['items']->{'plugin.id'} );
		$this->assertEquals( $saved['items']->{'plugin.id'}, $searched['items'][0]->{'plugin.id'} );
		$this->assertEquals( $saved['items']->{'plugin.status'}, $searched['items'][0]->{'plugin.status'} );
		$this->assertEquals( $saved['items']->{'plugin.position'}, $searched['items'][0]->{'plugin.position'} );
		$this->assertEquals( $saved['items']->{'plugin.provider'}, $searched['items'][0]->{'plugin.provider'} );
		$this->assertEquals( $saved['items']->{'plugin.config'}, $searched['items'][0]->{'plugin.config'} );
		$this->assertEquals( $saved['items']->{'plugin.typeid'}, $searched['items'][0]->{'plugin.typeid'} );
		$this->assertEquals( $saved['items']->{'plugin.label'}, $searched['items'][0]->{'plugin.label'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
