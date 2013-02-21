<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14869 2012-01-13 17:30:30Z nsendetzky $
 */

/**
 * Test class for MShop_Catalog_Item_Default.
 */
class MShop_Catalog_Item_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_values;
	protected $_listItems;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Item_DefaultTest');
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
		$listValues = array( 'id' => 1, 'type' => 'default', 'domain' => 'text' );
		$this->_listItems = array( 1 => new MShop_Common_Item_List_Default('catalog.list.', $listValues ) );

		$this->_values = array(
			'id' => 2,
			'code' => 'unit-test',
			'label' => 'unittest',
			'config' => array('testcategory' => '10'),
			'status' => 1,
			'siteid' => '99',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_node = new MW_Tree_Node_Default( $this->_values );
		$child = new MShop_Catalog_Item_Default( $this->_node );

		$this->_object = new MShop_Catalog_Item_Default( $this->_node, array( $child ), $this->_listItems );
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
	}


	public function testGetId()
	{
		$this->assertEquals( 2, $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( 5 );
		$this->assertEquals( 5, $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );

		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unit-test', $this->_object->getCode() );
	}


	public function testSetCode()
	{
		$this->_object->setCode( 'unit test' );
		$this->assertEquals( 'unit test', $this->_object->getCode() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'unit test' );
		$this->assertEquals( 'unit test', $this->_object->getLabel() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array('testcategory' => '10'), $this->_object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->_object->setConfig( array('unitcategory' => '12') );
		$this->assertEquals( array('unitcategory' => '12'), $this->_object->getConfig() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertEquals( 0, $this->_object->getStatus() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetSiteid()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testToArray()
	{
		$values = $this->_object->toArray();

		$this->assertEquals( count( $this->_values ), count( $values ) );

		$this->assertEquals( $this->_values['id'], $values['catalog.id'] );
		$this->assertEquals( $this->_values['label'], $values['catalog.label'] );
		$this->assertEquals( $this->_values['config'], $values['catalog.config'] );
		$this->assertEquals( $this->_values['status'], $values['catalog.status'] );
		$this->assertEquals( $this->_values['siteid'], $values['catalog.siteid'] );
		$this->assertEquals( $this->_values['code'], $values['catalog.code'] );
		$this->assertEquals( $this->_values['ctime'], $values['catalog.ctime']);
		$this->assertEquals( $this->_values['mtime'], $values['catalog.mtime']);
		$this->assertEquals( $this->_values['editor'], $values['catalog.editor']);
	}


	public function testHasChildren()
	{
		$this->assertTrue( $this->_object->hasChildren() );
	}


	public function testGetChildren()
	{
		$children = $this->_object->getChildren();
		$this->assertEquals( 1, count( $children ) );

		foreach( $children as $child ) {
			$this->assertInstanceOf( 'MShop_Catalog_Item_Interface', $child );
		}
	}


	public function testAddChild()
	{
		$this->_object->addChild( $this->_object );
		$this->assertEquals( 2, count( $this->_object->getChildren() ) );
	}


	public function testGetChild()
	{
		$this->assertInstanceOf( 'MShop_Catalog_Item_Interface', $this->_object->getChild( 0 ) );

		$this->setExpectedException( 'MShop_Catalog_Exception' );
		$this->_object->getChild( 1 );
	}


	public function testGetNode()
	{
		$this->assertInstanceOf( 'MW_Tree_Node_Interface', $this->_object->getNode() );
	}
}
