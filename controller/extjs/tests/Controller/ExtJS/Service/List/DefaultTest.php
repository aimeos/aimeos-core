<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Service_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Service_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Service_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.list.type.code' => 'unittype1' ) ) ) ),
			'sort' => 'service.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'service.list.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.label', 'unitlabel' ) );
		$resultService = $serviceManager->searchItems( $search );

		if( ( $item = reset( $resultService ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.list.type.domain' => 'product' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$serviceListTypeManager = Controller_ExtJS_Service_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $serviceListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.list.parentid' => $item->getId(),
				'service.list.typeid' => $resultType['items'][0]->{'service.list.type.id'},
				'service.list.domain' => 'product',
				'service.list.refid' => -1,
				'service.list.datestart' => '2000-01-01 00:00:00',
				'service.list.dateend' => '2001-01-01 00:00:00',
				'service.list.config' => array('test' => 'unit'),
				'service.list.position' => 1,
				'service.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.list.refid' => -1 ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.list.id'} );
		$this->assertEquals( $saved['items']->{'service.list.id'}, $searched['items'][0]->{'service.list.id'});
		$this->assertEquals( $saved['items']->{'service.list.parentid'}, $searched['items'][0]->{'service.list.parentid'});
		$this->assertEquals( $saved['items']->{'service.list.typeid'}, $searched['items'][0]->{'service.list.typeid'});
		$this->assertEquals( $saved['items']->{'service.list.domain'}, $searched['items'][0]->{'service.list.domain'});
		$this->assertEquals( $saved['items']->{'service.list.refid'}, $searched['items'][0]->{'service.list.refid'});
		$this->assertEquals( $saved['items']->{'service.list.datestart'}, $searched['items'][0]->{'service.list.datestart'});
		$this->assertEquals( $saved['items']->{'service.list.dateend'}, $searched['items'][0]->{'service.list.dateend'});
		$this->assertEquals( $saved['items']->{'service.list.config'}, $searched['items'][0]->{'service.list.config'});
		$this->assertEquals( $saved['items']->{'service.list.position'}, $searched['items'][0]->{'service.list.position'});
		$this->assertEquals( $saved['items']->{'service.list.status'}, $searched['items'][0]->{'service.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
