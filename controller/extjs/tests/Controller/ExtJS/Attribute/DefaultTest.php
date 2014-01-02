<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Attribute_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Attribute_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Attribute_Default( TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'attribute.code' => 'x' ) ),
					1 => array( '==' => (object) array( 'attribute.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'attribute.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'xl', $result['items'][0]->{'attribute.code'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'attribute.typeid' => $type->getId(),
				'attribute.domain' => 'product',
				'attribute.code' => 'test',
				'attribute.label' => 'test label',
				'attribute.position' => 1,
				'attribute.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'attribute.code' => 'test' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array($saved['items']->{'attribute.id'}) );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.id'} );
		$this->assertEquals( $saved['items']->{'attribute.id'}, $searched['items'][0]->{'attribute.id'} );
		$this->assertEquals( $saved['items']->{'attribute.typeid'}, $searched['items'][0]->{'attribute.typeid'} );
		$this->assertEquals( $saved['items']->{'attribute.domain'}, $searched['items'][0]->{'attribute.domain'} );
		$this->assertEquals( $saved['items']->{'attribute.code'}, $searched['items'][0]->{'attribute.code'} );
		$this->assertEquals( $saved['items']->{'attribute.label'}, $searched['items'][0]->{'attribute.label'} );
		$this->assertEquals( $saved['items']->{'attribute.position'}, $searched['items'][0]->{'attribute.position'} );
		$this->assertEquals( $saved['items']->{'attribute.status'}, $searched['items'][0]->{'attribute.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testCopyItem()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
		$listManager = $attributeManager->getSubManager('list');

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'xs' ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'attribute.id' => $item->getId(),
				'attribute.typeid' => $item->getTypeId(),
				'attribute.domain' => 'product',
				'attribute.code' => 'copiedXS',
				'attribute.label' => 'test label',
				'attribute.position' => 1,
				'attribute.status' => 0,
				'_copy' => true
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'attribute.code' => 'xs' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$search = $listManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.list.parentid', $item->getId() ) );
		$listItems = $listManager->searchItems( $search );

		$search = $listManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.list.parentid', $saved['items']->{'attribute.id'} ) );
		$copiedListItems = $listManager->searchItems( $search );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'attribute.id'} );
		$this->_object->deleteItems( $deleteParams );

		$this->assertTrue( !empty( $copiedListItems ) );
		$this->assertEquals( count($listItems), count( $copiedListItems ) );
	}

	public function testAbstractInit()
	{
		$expected = array('success' => true);
		$actual = $this->_object->init( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array('success' => true);
		$actual = $this->_object->finish( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->_object->getItemSchema();
		$expected = array(
			'name' => 'Attribute',
			'properties' => array(
				'attribute.id' => array(
					'description' => 'Attribute ID',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.siteid' => array(
					'description' => 'Attribute site',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.typeid' => array(
					'description' => 'Attribute type',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.domain' => array(
					'description' => 'Attribute domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.code' => array(
					'description' => 'Attribute code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.position' => array(
					'description' => 'Attribute position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.label' => array(
					'description' => 'Attribute label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.status' => array(
					'description' => 'Attribute status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.ctime' => array(
					'description' => 'Attribute create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.mtime' => array(
					'description' => 'Attribute modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.editor' => array(
					'description' => 'Attribute editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->_object->getSearchSchema();
		$expected = array(
			'criteria' => array(
				'attribute.id' => array(
					'description' => 'Attribute ID',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.domain' => array(
					'description' => 'Attribute domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.code' => array(
					'description' => 'Attribute code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.position' => array(
					'description' => 'Attribute position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.label' => array(
					'description' => 'Attribute label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.status' => array(
					'description' => 'Attribute status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.ctime' => array(
					'description' => 'Attribute create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.mtime' => array(
					'description' => 'Attribute modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.editor' => array(
					'description' => 'Attribute editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.code' => array(
					'description' => 'Attribute type code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.domain' => array(
					'description' => 'Attribute type domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.label' => array(
					'description' => 'Attribute type label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.status' => array(
					'description' => 'Attribute type status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.type.ctime' => array(
					'description' => 'Attribute type create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.type.mtime' => array(
					'description' => 'Attribute type modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.type.editor' => array(
					'description' => 'Attribute type editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.domain' => array(
					'description' => 'Attribute list domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.refid' => array(
					'description' => 'Attribute list reference ID',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.datestart' => array(
					'description' => 'Attribute list start date',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.dateend' => array(
					'description' => 'Attribute list end date',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.config' => array(
					'description' => 'Attribute list config',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.position' => array(
					'description' => 'Attribute list position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.list.status' => array(
					'description' => 'Attribute list status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.list.ctime' => array(
					'description' => 'Attribute list create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.mtime' => array(
					'description' => 'Attribute list modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.editor' => array(
					'description' => 'Attribute list editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.type.code' => array(
					'description' => 'Attribute list type code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.type.domain' => array(
					'description' => 'Attribute list type domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.type.label' => array(
					'description' => 'Attribute list type label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.list.type.status' => array(
					'description' => 'Attribute list type status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.list.type.ctime' => array(
					'description' => 'Attribute list type create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.type.mtime' => array(
					'description' => 'Attribute list type modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.list.type.editor' => array(
					'description' => 'Attribute list type editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractInitCriteriaException()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'attribute.code' => 'x' ) ) ) ),
			'sort' => 'attribute.code',
			'dir' => 'NO_SORTATION',
			'start' => 0,
			'limit' => 1,
		);

		$this->setExpectedException('Controller_ExtJS_Exception');
		$this->_object->searchItems( $params );
	}

}
