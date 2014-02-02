<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Locale_Site_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Locale_Site_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Locale_Site_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.site.code' => 'unittest' ) ) ) ),
			'sort' => 'locale.site.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unittest', $result['items'][0]->{'locale.site.code'} );
	}


	public function testGetTree()
	{
		$params = (object) array( 'items' => 'root' );
		$result = $this->_object->getTree( $params );

		$this->assertEquals( 'Root', $result['items']->{'locale.site.label'} );
		$this->assertGreaterThanOrEqual( 2, $result['items']->{'children'} );


		$ids = array();
		foreach( $result['items']->{'children'} as $child ) {
			$ids[] = $child->{'locale.site.id'};
		}

		$params = (object) array( 'items' => $ids );
		$result = $this->_object->getTree( $params );

		$this->assertGreaterThanOrEqual( 2, $result['items'] );
	}


	public function testInsertDeleteItems()
	{
		$insertParams = (object) array(
			'items' => (object) array(
				'locale.site.code' => 'testnode',
				'locale.site.label' => 'controller test node',
				'locale.site.status' => 0,
			),
			'parentid' => null,
		);
		$saved = $this->_object->insertItems( $insertParams );

		$params = (object) array( 'items' => $saved['items']->{'locale.site.id'} );
		$this->_object->deleteItems( $params );

		try {
			$this->_object->getTree( $params );
		} catch ( MShop_Exception $me){
			return;
		} catch ( MW_Tree_Exception $mte){
			return;
		}

		$this->fail( 'An expected exception has not been raised' );
	}


	public function testSaveItems()
	{
		$insertParams = (object) array(
			'items' => (object) array(
				'locale.site.code' => 'testnode',
				'locale.site.label' => 'controller test node',
				'locale.site.status' => 0,
			),
			'parentid' => null,
		);
		$inserted = $this->_object->insertItems( $insertParams );

		$saveParams = (object) array(
			'items' => (object) array(
				'locale.site.id' => $inserted['items']->{'locale.site.id'},
				'locale.site.code' => 'testnode2',
				'locale.site.label' => 'controller test',
				'locale.site.status' => 0,
			),
		);
		$saved = $this->_object->saveItems( $saveParams );

		$params = (object) array( 'items' => $saved['items']->{'locale.site.id'} );
		$newroot = $this->_object->getTree( $params );

		$params = (object) array( 'items' => $inserted['items']->{'locale.site.id'} );
		$this->_object->deleteItems( $params );

		$this->assertEquals( $inserted['items']->{'locale.site.id'}, $newroot['items']->{'locale.site.id'} );
		$this->assertEquals( 'testnode2', $newroot['items']->{'locale.site.code'} );
		$this->assertEquals( 'controller test', $newroot['items']->{'locale.site.label'} );
	}


	public function testMoveItems()
	{
		$this->markTestIncomplete( 'Operation not available yet' );
	}


	// doesnt call moveItems()
	public function testMoveItemsNoAction()
	{
		$moveParams = (object) array(
			'items' => array(),
			'oldparentid' => null,
			'newparentid' => null,
			'refid' => null,
		);

		$result = $this->_object->moveItems( $moveParams );

		$this->assertTrue( $result['success'] );
	}

	// call moveItems(), throws "not implemented" yet
	public function testMoveItemsException()
	{
		$moveParams = (object) array(
			'items' => null,
			'oldparentid' => null,
			'newparentid' => null,
			'refid' => null
		);

		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->_object->moveItems( $moveParams );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Locale_Site.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.saveItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.searchItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
				),
				"returns" => "array",
			),
			'Locale_Site.getTree' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.insertItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "string", "name" => "parentid", "optional" => true ),
					array( "type" => "string", "name" => "refid", "optional" => true ),
				),
				"returns" => "array",
			),
			'Locale_Site.moveItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items", "optional" => false ),
					array( "type" => "string","name" => "oldparentid", "optional" => false ),
					array( "type" => "string","name" => "newparentid", "optional" => false ),
					array( "type" => "string","name" => "refid", "optional" => true ),
				),
				"returns" => "array",
			),
		);

		$actual = $this->_object->getServiceDescription();

		$this->assertEquals($expected, $actual);
	}
}
