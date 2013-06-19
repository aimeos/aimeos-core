<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Common_Item_Type_Default.
 */
class MShop_Common_Item_Type_DefaultTest extends MW_Unittest_Testcase
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
			'code' => 'code',
			'domain' => 'domain',
			'label' => 'label',
			'status' => 1,
			'siteid' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Common_Item_Type_Default( '', $this->_values );
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

	public function testGetCode()
	{
		$this->assertEquals( 'code', $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->_object->setCode( 'code' );
		$this->assertFalse($this->_object->isModified());
		$this->assertEquals( 'code', $this->_object->getCode() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'domain', $this->_object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->_object->setDomain( 'domain' );
		$this->assertFalse($this->_object->isModified());
		$this->assertEquals( 'domain', $this->_object->getDomain() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'label', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel( 'label' );
		$this->assertFalse($this->_object->isModified());
		$this->assertEquals( 'label', $this->_object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 1 );
		$this->assertFalse($this->_object->isModified());
		$this->assertEquals( 1, $this->_object->getStatus() );
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
		$this->assertEquals( $this->_object->getCode(), $arrayObject['code'] );
		$this->assertEquals( $this->_object->getDomain(), $arrayObject['domain'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['label'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['status'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['siteid'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
}
