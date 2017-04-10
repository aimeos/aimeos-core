<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Coupon\Item\Code;


/**
 * Test class for \Aimeos\MShop\Coupon\Item\Code\Standard.
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
			'coupon.code.id' => '1',
			'coupon.code.siteid' => 123,
			'coupon.code.parentid' => '2',
			'coupon.code.code' => 'abcd',
			'coupon.code.count' => '100',
			'coupon.code.datestart' => null,
			'coupon.code.dateend' => null,
			'coupon.code.mtime' => '2011-01-01 00:00:02',
			'coupon.code.ctime' => '2011-01-01 00:00:01',
			'coupon.code.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Coupon\Item\Code\Standard( $this->values );
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
	}

	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( '1' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertFalse( false, $this->object->isModified() );
		$this->assertEquals( 1, $this->object->getId() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( true, $this->object->isModified() );
		$this->assertEquals( null, $this->object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 2, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$return = $this->object->setParentId( '3' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( 3, $this->object->getParentId() );
		$this->assertEquals( true, $this->object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'abcd', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$return = $this->object->setCode( 'dcba' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( 'dcba', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCount()
	{
		$this->assertEquals( 100, $this->object->getCount() );
	}

	public function testSetCount()
	{
		$return = $this->object->setCount( 50 );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( 50, $this->object->getCount() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateStart()
	{
		$this->assertNull( $this->object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateEnd()
	{
		$this->assertNull( $this->object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Code\Iface', $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );
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
		$this->assertEquals( 'coupon/code', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Coupon\Item\Code\Standard();

		$list = array(
			'coupon.code.id' => 1,
			'coupon.code.parentid' => 2,
			'coupon.code.code' => 'test',
			'coupon.code.count' => 100,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['coupon.code.id'], $item->getId() );
		$this->assertEquals( $list['coupon.code.parentid'], $item->getParentId() );
		$this->assertEquals( $list['coupon.code.code'], $item->getCode() );
		$this->assertEquals( $list['coupon.code.count'], $item->getCount() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['coupon.code.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['coupon.code.code'] );
		$this->assertEquals( $this->object->getCount(), $arrayObject['coupon.code.count'] );
		$this->assertEquals( $this->object->getParentId(), $arrayObject['coupon.code.parentid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['coupon.code.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['coupon.code.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['coupon.code.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
