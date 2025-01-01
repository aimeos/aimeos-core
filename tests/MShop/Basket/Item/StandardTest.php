<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2025
 */

namespace Aimeos\MShop\Basket\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $basket;
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'basket.id' => '123-456-789',
			'basket.siteid' => '1.',
			'basket.customerid' => '11',
			'basket.name' => 'testbasket',
			'basket.mtime' => '2011-01-01 00:00:02',
			'basket.ctime' => '2011-01-01 00:00:01',
			'basket.editor' => 'unitTestUser',
		);

		$this->basket = \Aimeos\MShop::create( \TestHelper::context(), 'order' )->create()->off();
		$this->object = new \Aimeos\MShop\Basket\Item\Standard( $this->values, $this->basket );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket );
	}


	public function testGetId()
	{
		$this->assertEquals( '123-456-789', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( '987-654-321' );

		$this->assertInstanceOf( \Aimeos\MShop\Basket\Item\Iface::class, $return );
		$this->assertEquals( '987-654-321', $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '1.', $this->object->getSiteId() );
	}


	public function testGetCustomerId()
	{
		$this->assertEquals( '11', $this->object->getCustomerId() );
	}


	public function testSetCustomerId()
	{
		$return = $this->object->setCustomerId( '12' );

		$this->assertInstanceOf( \Aimeos\MShop\Basket\Item\Iface::class, $return );
		$this->assertEquals( '12', $this->object->getCustomerId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGet()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $this->object->getItem() );
	}


	public function testSetItem()
	{
		$return = $this->object->setItem( $this->basket );

		$this->assertInstanceOf( \Aimeos\MShop\Basket\Item\Iface::class, $return );
		$this->assertSame( $this->basket, $this->object->getItem() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'testbasket', $this->object->getName() );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'unittest' );

		$this->assertInstanceOf( \Aimeos\MShop\Basket\Item\Iface::class, $return );
		$this->assertEquals( 'unittest', $this->object->getName() );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'basket', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Basket\Item\Standard();

		$list = $entries = array(
			'basket.id' => '123-456',
			'basket.customerid' => '123',
			'basket.name' => 'test basket name',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['basket.id'], $item->getId() );
		$this->assertEquals( $list['basket.customerid'], $item->getCustomerId() );
		$this->assertEquals( $list['basket.name'], $item->getName() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['basket.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['basket.siteid'] );
		$this->assertEquals( $this->object->getCustomerId(), $list['basket.customerid'] );
		$this->assertEquals( $this->object->getName(), $list['basket.name'] ); ;
		$this->assertEquals( $this->object->getTimeModified(), $list['basket.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['basket.ctime'] );
		$this->assertEquals( $this->object->editor(), $list['basket.editor'] );
	}
}
