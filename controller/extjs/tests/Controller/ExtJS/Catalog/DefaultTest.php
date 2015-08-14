<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Catalog_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_rootnode;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Catalog_Default( TestHelper::getContext() );

		$params = (object) array( 'site' => 'unittest', 'items' => 'root' );

		$result = $this->_object->getTree( $params );
		$this->_rootnode = $result['items'];
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		unset( $this->_rootnode );
	}


	public function testGetTree()
	{
		$this->assertEquals( 'Root', $this->_rootnode->{'catalog.label'} );
		$this->assertEquals( 1, $this->_rootnode->{'catalog.status'} );
		$this->assertEquals( 2, count( $this->_rootnode->{'children'} ) );
	}


	public function testInsertSaveDeleteItems()
	{
		$insertParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'catalog.code' => 'test node',
				'catalog.label' => 'controller test node',
				'catalog.config' => array( 'unittest' => '1234' ),
				'catalog.status' => 0,
			),
			'parentid' => $this->_rootnode->{'catalog.id'},
			'refid' => $this->_rootnode->{'children'}[1]->{'catalog.id'},
		);
		$inserted = $this->_object->insertItems( $insertParams );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'catalog.id' => $inserted['items']->{'catalog.id'},
				'catalog.code' => 'test node 2',
				'catalog.label' => 'controller test',
				'catalog.config' => array( 'testunit' => '4321' ),
				'catalog.status' => 0,
			),
		);
		$this->_object->saveItems( $saveParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->_rootnode->{'catalog.id'} );
		$newroot = $this->_object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $inserted['items']->{'catalog.id'} );
		$this->_object->deleteItems( $params );

		$this->assertEquals( $inserted['items']->{'catalog.id'}, $newroot['items']->{'children'}[1]->{'catalog.id'} );
		$this->assertEquals( 'controller test', $newroot['items']->{'children'}[1]->{'catalog.label'} );
	}


	public function testMoveItems()
	{
		$insertParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'catalog.code' => 'test node',
				'catalog.label' => 'controller test node',
				'status' => false,
			),
			'parentid' => $this->_rootnode->{'catalog.id'},
			'refid' => $this->_rootnode->{'children'}[1]->{'catalog.id'},
		);
		$inserted = $this->_object->insertItems( $insertParams );

		$moveParams = (object) array(
			'site' => 'unittest',
			'items' => $inserted['items']->{'catalog.id'},
			'oldparentid' => $this->_rootnode->{'catalog.id'},
			'newparentid' => $this->_rootnode->{'children'}[0]->{'catalog.id'},
		);
		$this->_object->moveItems( $moveParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->_rootnode->{'catalog.id'} );
		$newroot = $this->_object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->_rootnode->{'children'}[0]->{'catalog.id'} );
		$newparent = $this->_object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $inserted['items']->{'catalog.id'} );
		$this->_object->deleteItems( $params );

		$this->assertEquals( $this->_rootnode->{'children'}[1]->{'catalog.id'}, $newroot['items']->{'children'}[1]->{'catalog.id'} );
		$this->assertEquals( $inserted['items']->{'catalog.id'}, $newparent['items']->{'children'}[3]->{'catalog.id'} );
		$this->assertEquals( 'controller test node', $newparent['items']->{'children'}[3]->{'catalog.label'} );
	}


	public function testGetSearchSchema()
	{
		$this->assertInternalType( 'array', $this->_object->getSearchSchema() );
	}


	public function testGetItemSchema()
	{
		$this->assertInternalType( 'array', $this->_object->getItemSchema() );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->_object->getServiceDescription();

		$expected = array(
			'Catalog.getTree' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Catalog.insertItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "string", "name" => "parentid", "optional" => true ),
					array( "type" => "string", "name" => "refid", "optional" => true ),
				),
				"returns" => "array",
			),
			'Catalog.moveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "string", "name" => "oldparentid", "optional" => false ),
					array( "type" => "string", "name" => "newparentid", "optional" => false ),
					array( "type" => "string", "name" => "refid", "optional" => true ),
				),
				"returns" => "array",
			),
			'Catalog.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Catalog.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Catalog.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
			'Catalog.init' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
			'Catalog.finish' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testFinish()
	{
		$result = $this->_object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}
