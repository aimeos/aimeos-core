<?php

namespace Aimeos\Controller\ExtJS\Catalog;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $rootnode;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Catalog\Standard( \TestHelper::getContext() );

		$params = (object) array( 'site' => 'unittest', 'items' => 'root' );

		$result = $this->object->getTree( $params );
		$this->rootnode = $result['items'];
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->rootnode );
	}


	public function testGetTree()
	{
		$this->assertEquals( 'Root', $this->rootnode->{'catalog.label'} );
		$this->assertEquals( 1, $this->rootnode->{'catalog.status'} );
		$this->assertEquals( 2, count( $this->rootnode->{'children'} ) );
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
			'parentid' => $this->rootnode->{'catalog.id'},
			'refid' => $this->rootnode->{'children'}[1]->{'catalog.id'},
		);
		$inserted = $this->object->insertItems( $insertParams );

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
		$this->object->saveItems( $saveParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->rootnode->{'catalog.id'} );
		$newroot = $this->object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $inserted['items']->{'catalog.id'} );
		$this->object->deleteItems( $params );

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
			'parentid' => $this->rootnode->{'catalog.id'},
			'refid' => $this->rootnode->{'children'}[1]->{'catalog.id'},
		);
		$inserted = $this->object->insertItems( $insertParams );

		$moveParams = (object) array(
			'site' => 'unittest',
			'items' => $inserted['items']->{'catalog.id'},
			'oldparentid' => $this->rootnode->{'catalog.id'},
			'newparentid' => $this->rootnode->{'children'}[0]->{'catalog.id'},
		);
		$this->object->moveItems( $moveParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->rootnode->{'catalog.id'} );
		$newroot = $this->object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $this->rootnode->{'children'}[0]->{'catalog.id'} );
		$newparent = $this->object->getTree( $params );

		$params = (object) array( 'site' => 'unittest', 'items' => $inserted['items']->{'catalog.id'} );
		$this->object->deleteItems( $params );

		$this->assertEquals( $this->rootnode->{'children'}[1]->{'catalog.id'}, $newroot['items']->{'children'}[1]->{'catalog.id'} );
		$this->assertEquals( $inserted['items']->{'catalog.id'}, $newparent['items']->{'children'}[3]->{'catalog.id'} );
		$this->assertEquals( 'controller test node', $newparent['items']->{'children'}[3]->{'catalog.label'} );
	}


	public function testGetSearchSchema()
	{
		$this->assertInternalType( 'array', $this->object->getSearchSchema() );
	}


	public function testGetItemSchema()
	{
		$this->assertInternalType( 'array', $this->object->getItemSchema() );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->object->getServiceDescription();

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
		$result = $this->object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}
