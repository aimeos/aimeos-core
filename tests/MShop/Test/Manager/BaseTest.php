<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\MShop\Test\Manager;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Test\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object );
	}


	public function testClear()
	{
		$result = $this->object->clear( [$this->context->locale()->getSiteId()] );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $result );
	}


	public function testCreate()
	{
		$item = $this->object->create( [
			'id' => 123,
			'siteid' => '1.',
			'key' => 'somekey',
			'value' => 'someval',
			'json' => ['key' => 'value'],
			'mtime' => '2000-01-01 00:00:00',
			'ctime' => '2000-01-01 00:00:00',
			'editor' => 'testeditor'
		] );

		$this->assertEquals( 123, $item->getId() );
		$this->assertEquals( '1.', $item->getSiteId() );
		$this->assertEquals( 'somekey', $item->get( 'key' ) );
		$this->assertEquals( 'someval', $item->get( 'value' ) );
		$this->assertEquals( ['key' => 'value'], $item->get( 'json' ) );
		$this->assertEquals( '2000-01-01 00:00:00', $item->getTimeModified() );
		$this->assertEquals( '2000-01-01 00:00:00', $item->getTimeCreated() );
		$this->assertEquals( 'testeditor', $item->editor() );
	}


	public function testCreateEmpty()
	{
		$data = [
			'id' => 123,
			'siteid' => '1.',
			'key' => 'somekey',
			'value' => 'someval',
			'json' => ['key' => 'value'],
			'mtime' => '2000-01-01 00:00:00',
			'ctime' => '2000-01-01 00:00:00',
			'editor' => 'testeditor'
		];
		$item = $this->object->create()->fromArray( $data, true );

		$this->assertEquals( 123, $item->getId() );
		$this->assertEquals( '1.', $item->getSiteId() );
		$this->assertEquals( 'somekey', $item->get( 'key' ) );
		$this->assertEquals( 'someval', $item->get( 'value' ) );
		$this->assertEquals( ['key' => 'value'], $item->get( 'json' ) );
	}


	public function testDelete()
	{
		$result = $this->object->delete( -1 );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $result );
	}


	public function testFilter()
	{
		$result = $this->object->filter();
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $result );
	}


	public function testSave()
	{
		$item = $this->object->create()
			->set( 'key', 'somekey' )
			->set( 'value', 'someval' )
			->set( 'json', ['somekey' => 'someval'] );

		$result = $this->object->save( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $result );

		$this->object->clear( [$this->context->locale()->getSiteId()] );
	}


	public function testSearch()
	{
		$item = $this->object->create()
			->set( 'key', 'somekey' )
			->set( 'value', 'someval' );

		$this->object->save( $item );
		$result = $this->object->search( $this->object->filter() );

		$this->assertInstanceOf( \Aimeos\Map::class, $result );
		$this->assertEquals( 'somekey', $result->first()?->get( 'key' ) );
		$this->assertEquals( 'someval', $result->first()?->get( 'value' ) );

		$this->object->clear( [$this->context->locale()->getSiteId()] );
	}


	public function testIterate()
	{
		$item = $this->object->create()
			->set( 'key', 'somekey' )
			->set( 'value', 'someval' );

		$this->object->save( $item );

		$cursor = $this->object->cursor( $this->object->filter() );
		$result = $this->object->iterate( $cursor );

		$this->assertInstanceOf( \Aimeos\Map::class, $result );
		$this->assertEquals( 'somekey', $result->first()?->get( 'key' ) );
		$this->assertEquals( 'someval', $result->first()?->get( 'value' ) );

		$this->object->clear( [$this->context->locale()->getSiteId()] );
	}


	public function testGet()
	{
		$item = $this->object->create()
			->set( 'key', 'somekey' )
			->set( 'value', 'someval' );

		$item = $this->object->save( $item );
		$result = $this->object->get( $item->getId() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $result );
		$this->assertEquals( 'somekey', $result->get( 'key' ) );
		$this->assertEquals( 'someval', $result->get( 'value' ) );

		$this->object->clear( [$this->context->locale()->getSiteId()] );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( ['test'], $this->object->getResourceType() );
	}
}



class Standard extends \Aimeos\MShop\Common\Manager\Base implements \Aimeos\MShop\Common\Manager\Iface
{
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( [
			'key' => [
				'label' => 'Key',
				'internalcode' => 'mtes."key"',
			],
			'value' => [],
			'json' => [
				'type' => 'json'
			],
		] );
	}
}
