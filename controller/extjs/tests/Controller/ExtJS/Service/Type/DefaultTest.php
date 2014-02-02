<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Service_Type_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Service_Type_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Service_Type_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.type.code' => 'delivery' ) ) ) ),
			'sort' => 'service.type.code'
		);

		$result = $this->_object->searchItems( $params );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No service type found' );
		}

		$this->assertEquals( 1, count( $type ) );
		$this->assertEquals( 'delivery', reset( $type )->{'service.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.type.code' => 'test code',
				'service.type.label' => 'test label',
				'service.type.status' => 1,
				'service.type.domain' => 'service'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.type.code' => 'test code' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.type.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.type.id'} );
		$this->assertEquals( $saved['items']->{'service.type.id'}, $searched['items'][0]->{'service.type.id'} );
		$this->assertEquals( $saved['items']->{'service.type.code'}, $searched['items'][0]->{'service.type.code'} );
		$this->assertEquals( $saved['items']->{'service.type.domain'}, $searched['items'][0]->{'service.type.domain'} );
		$this->assertEquals( $saved['items']->{'service.type.label'}, $searched['items'][0]->{'service.type.label'} );
		$this->assertEquals( $saved['items']->{'service.type.status'}, $searched['items'][0]->{'service.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
