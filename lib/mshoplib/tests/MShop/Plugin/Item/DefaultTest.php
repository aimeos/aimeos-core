<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Test class for MShop_Plugin_Item_Default
 */
class MShop_Plugin_Item_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Item_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 123,
			'siteid'=>99,
			'typeid' => 2,
			'label' => 'unitTestPlugin',
			'type' => 'order',
			'provider' => 'provider',
			'config' => array( 'limit'=>'40' ),
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Plugin_Item_Default( $this->values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetId()
	{
		$this->assertEquals( 123, $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals(99, $this->_object->getSiteId());
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->_object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->_object->setTypeId(99);
		$this->assertEquals(99, $this->_object->getTypeId());

		$this->assertTrue( $this->_object->isModified() );
	}

	
	public function testGetLabel()
	{
		$this->assertEquals( 'unitTestPlugin', $this->_object->getLabel() );
	}
	
	
	public function testSetLabel()
	{
		$this->_object->setLabel( 'anotherLabel' );
		$this->assertEquals( 'anotherLabel', $this->_object->getLabel() );
		$this->assertEquals( true, $this->_object->isModified() );
	}
	

	public function testGetProvider()
	{
		$this->assertEquals( 'provider', $this->_object->getProvider() );
	}
	

	public function testSetProvider()
	{
		$this->_object->setProvider( 'newProvider' );
		$this->assertEquals( 'newProvider', $this->_object->getProvider() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array('limit'=>'40'), $this->_object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->_object->setConfig( array('threshold'=>'20.00') );
		$this->assertEquals( array('threshold'=>'20.00'), $this->_object->getConfig() );
		$this->assertEquals( true, $this->_object->isModified() );
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
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['plugin.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['plugin.siteid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['plugin.type'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['plugin.typeid'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['plugin.label'] );
		$this->assertEquals( $this->_object->getProvider(), $arrayObject['plugin.provider'] );
		$this->assertEquals( $this->_object->getConfig(), $arrayObject['plugin.config'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['plugin.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['plugin.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['plugin.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['plugin.editor'] );
	}
}
