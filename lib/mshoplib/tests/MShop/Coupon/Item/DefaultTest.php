<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Item_Example.
 */
class MShop_Coupon_Item_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Coupon_Item_DefaultTest');
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
		$this->values = array(
			'siteid' => 123,
			'label' => 'test coupon',
			'provider' => 'Example',
			'config' => array('key'=>'test'),
			'start' => null,
			'end' => null,
			'status' => true,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Coupon_Item_Default( $this->values );
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
		$this->assertNULL( $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId( 2 );
		$this->assertEquals( 2, $this->_object->getId() );

		$this->assertFalse(false, $this->_object->isModified() );

		$this->_object->setId( null );

		$this->assertEquals( null, $this->_object->getId() );
		$this->assertEquals( true, $this->_object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->_object->getSiteId() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'test coupon', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel('unitTest');
		$this->assertEquals( 'unitTest', $this->_object->getLabel() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetDateStart()
	{
		$this->assertNull( $this->_object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$this->_object->setDateStart( '2010-04-22 06:22:22' );
		$this->assertEquals( '2010-04-22 06:22:22', $this->_object->getDateStart() );
	}

	public function testGetDateEnd()
	{
		$this->assertNull( $this->_object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$this->_object->setDateEnd( '2010-05-22 06:22:22' );
		$this->assertEquals( '2010-05-22 06:22:22', $this->_object->getDateEnd() );
	}

	public function testGetProvider()
	{
		$this->assertEquals( 'Example', $this->_object->getProvider() );
	}

	public function testSetProvider()
	{
		$this->_object->setProvider( 'Test' );
		$this->assertEquals( 'Test', $this->_object->getProvider() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array('key'=>'test'), $this->_object->getConfig() );
	}

	public function testSetConfig()
	{
		$this->_object->setConfig( array('value'=>1) );
		$this->assertEquals( array('value'=>1), $this->_object->getConfig() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 14 );
		$this->assertEquals( 14, $this->_object->getStatus() );
		$this->assertTrue( $this->_object->isModified() );
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
		$this->assertEquals( (count( $this->values ) + 1), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['coupon.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['coupon.siteid'] );
		$this->assertEquals( $this->_object->getConfig(), $arrayObject['coupon.config'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['coupon.label'] );
		$this->assertEquals( $this->_object->getProvider(), $arrayObject['coupon.provider'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['coupon.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['coupon.ctime']);
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['coupon.mtime']);
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['coupon.editor']);
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}
