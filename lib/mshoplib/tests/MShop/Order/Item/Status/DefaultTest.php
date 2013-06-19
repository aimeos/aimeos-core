<?php 
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Order_Item_Status_Default.
 */

class MShop_Order_Item_Status_DefaultTest extends MW_Unittest_Testcase
{

	private $_object;
	private $_values;
	
	/**
	* Runs the test methods of this class.
	*
	* @access public
	* @static
	*/
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';
		
		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Item_Status_DefaultTest');
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
		$orderManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext());
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		
		$this->_values = array(
			'id' => 1,
			'siteid'=>99,
			'parentid'=>11,
			'type' => 'teststatus',
			'value' => 'this is a value from unittest',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);
		
		$this->_object = new MShop_Order_Item_Status_Default($this->_values);
		
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
		$this->assertEquals( $this->_values['id'], $this->_object->getId() );
	}
		
	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals(null, $this->_object->getId() );
		$this->assertTrue($this->_object->isModified());
	
		$this->_object->setId(15);
		$this->assertEquals(15, $this->_object->getId() );
		$this->assertFalse($this->_object->isModified());
	
		$this->setExpectedException('MShop_Exception');
		$this->_object->setId(6);
	}
	
	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}
	
	public function testGetParentId()
	{
		$this->assertEquals( 11, $this->_object->getParentId() );
	}
	
	public function testSetParentId()
	{
		$this->_object->setParentId(12);
		$this->assertEquals( 12, $this->_object->getParentId() );
		$this->assertTrue($this->_object->isModified());
	}
	
	public function testGetType()
	{
		$this->assertEquals( 'teststatus', $this->_object->getType() );
	}
	
	public function testSetType()
	{
		$this->_object->setType('unittest');
		$this->assertEquals( 'unittest', $this->_object->getType() );
		$this->assertTrue($this->_object->isModified());
	}
	
	public function testGetValue()
	{
		$this->assertEquals( "this is a value from unittest", $this->_object->getValue() );
	}
	
	public function testSetValue()
	{
		$this->_object->setValue('was changed by unittest');
		$this->assertEquals( 'was changed by unittest', $this->_object->getValue() );
		$this->assertTrue($this->_object->isModified());
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
		$list = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $list ) );
		
		$this->assertEquals( $this->_object->getId(), $list['order.status.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['order.status.siteid'] );
		$this->assertEquals( $this->_object->getParentId(), $list['order.status.parentid'] );
		$this->assertEquals( $this->_object->getType(), $list['order.status.type'] );;
		$this->assertEquals( $this->_object->getValue(), $list['order.status.value'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.status.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['order.status.ctime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['order.status.editor'] );
	
	}
	
	
}