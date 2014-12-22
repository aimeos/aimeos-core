<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Test class for MShop_Product_Item_Property_Default.
 */
class MShop_Product_Item_Property_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 987,
			'parentid' => 11,
			'siteid' => 99,
			'typeid' => 44,
			'langid' => 'en',
			'type' => 'width',
			'value' => '30.0',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Product_Item_Property_Default( $this->_values );
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
		$this->assertEquals( 987, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertNull( $this->_object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->_object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$this->_object->setLanguageId('fr');
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 'fr', $this->_object->getLanguageId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 11, $this->_object->getParentId() );
	}

	public function testSetParentId()
	{
		$this->_object->setParentId( 22 );
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 22, $this->_object->getParentId() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->_object->getTypeId() );
	}
	
	public function testSetTypeId()
	{
		$this->_object->setTypeId(33);
		$this->assertTrue( $this->_object->isModified() );
	
		$this->assertEquals( 33, $this->_object->getTypeId() );
	}
	
	public function testGetType()
	{
		$this->assertEquals( 'width', $this->_object->getType() );
	}

	public function testGetValue()
	{
		$this->assertEquals( '30.0', $this->_object->getValue() );
	}

	public function testSetValue()
	{
		$this->_object->setValue( '15.00' );
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( '15.00', $this->_object->getValue() );
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

		$this->assertEquals( $this->_object->getId(), $arrayObject['product.property.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['product.property.siteid'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['product.property.typeid'] );
		$this->assertEquals( $this->_object->getLanguageId(), $arrayObject['product.property.languageid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['product.property.type'] );
		$this->assertEquals( $this->_object->getValue(), $arrayObject['product.property.value'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['product.property.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['product.property.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['product.property.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}
