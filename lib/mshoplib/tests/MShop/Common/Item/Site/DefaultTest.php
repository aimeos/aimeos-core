<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 787 2012-06-19 08:43:22Z nsendetzky $
 */


/**
 * Test class for MShop_Common_Item_Site_Default.
 */
class MShop_Common_Item_Site_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_values;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Common_Item_Type_DefaultTest');
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
		$this->_values = array(
				'id'   => 1,
				'parentid' => 33,
				'siteid' => 1,
				'value' => 0,
				'mtime' => '2011-01-01 00:00:02',
				'ctime' => '2011-01-01 00:00:01',
				'editor' => 'unitTestUser'
		);
	
		$this->_object = new MShop_Common_Item_Site_Default( '', $this->_values );
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
	
	
	public function testGetId()
	{
		$this->assertEquals( 1, $this->_object->getId() );
	}
	
	
	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue($this->_object->isModified());
		$this->assertNull( $this->_object->getId());
	}
	
	
	public function testGetParentId()
	{
		$this->assertEquals( 33, $this->_object->getParentId() );
	}
	
	
	public function testSetParentId()
	{
		$this->_object->setParentId( 33 );
		$this->assertFalse( $this->_object->isModified() );
		$this->assertEquals( 33, $this->_object->getParentId() );
		$this->_object->setParentId( 35 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 35, $this->_object->getParentId() );
	}
	
	
	public function testGetValue()
	{
		$this->assertEquals( 0, $this->_object->getValue() );
	}
	
	
	public function testSetValue()
	{
		$this->_object->setValue( 0 );
		$this->assertFalse( $this->_object->isModified() );
		$this->assertEquals( 0, $this->_object->getValue() );
		$this->_object->setValue( 1 );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 1, $this->_object->getValue() );
	}
	
	
	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->_object->getSiteId() );
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
	
	
	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );
	
		$this->assertEquals( $this->_object->getId(), $arrayObject['id'] );
		$this->assertEquals( $this->_object->getParentId(), $arrayObject['parentid'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['siteid'] );
		$this->assertEquals( $this->_object->getValue(), $arrayObject['value'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['editor'] );
	}
	
	
	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
	
}