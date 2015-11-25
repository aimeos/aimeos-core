<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */


namespace Aimeos\MShop\Product\Item\Property;


/**
 * Test class for \Aimeos\MShop\Product\Item\Property\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'product.property.id' => 987,
			'product.property.parentid' => 11,
			'product.property.siteid' => 99,
			'product.property.typeid' => 44,
			'product.property.languageid' => 'en',
			'product.property.type' => 'width',
			'product.property.value' => '30.0',
			'product.property.mtime' => '2011-01-01 00:00:02',
			'product.property.ctime' => '2011-01-01 00:00:01',
			'product.property.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Product\Item\Property\Standard( $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 987, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId(null);
		$this->assertTrue( $this->object->isModified() );

		$this->assertNull( $this->object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$this->object->setLanguageId('fr');
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'fr', $this->object->getLanguageId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 11, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$this->object->setParentId( 22 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 22, $this->object->getParentId() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->object->setTypeId(33);
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 33, $this->object->getTypeId() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'width', $this->object->getType() );
	}

	public function testGetValue()
	{
		$this->assertEquals( '30.0', $this->object->getValue() );
	}

	public function testSetValue()
	{
		$this->object->setValue( '15.00' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '15.00', $this->object->getValue() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'product/property', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Product\Item\Property\Standard();

		$list = array(
			'product.property.parentid' => 1,
			'product.property.typeid' => 2,
			'product.property.languageid' => 'de',
			'product.property.value' => 'value',
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['product.property.parentid'], $item->getParentId());
		$this->assertEquals($list['product.property.typeid'], $item->getTypeId());
		$this->assertEquals($list['product.property.languageid'], $item->getLanguageId());
		$this->assertEquals($list['product.property.value'], $item->getValue());
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.property.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.property.siteid'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['product.property.typeid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['product.property.languageid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.property.type'] );
		$this->assertEquals( $this->object->getValue(), $arrayObject['product.property.value'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.property.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.property.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.property.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
