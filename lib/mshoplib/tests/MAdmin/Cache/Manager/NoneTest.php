<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MAdmin\Cache\Manager;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MAdmin\Cache\Manager\None( $this->context );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Cache\Item\Iface::class, $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'cache', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MAdmin\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );

		$this->assertTrue( $this->object->searchItems( $search )->isEmpty() );
	}


	public function testGetItem()
	{
		$this->expectException( \Aimeos\MAdmin\Cache\Exception::class );
		$this->object->getItem( 'unittest' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->saveItem( $this->object->createItem() );
		$this->object->deleteItem( $item->getId() );

		$this->assertInstanceOf( \Aimeos\MAdmin\Cache\Item\Iface::class, $item );
	}
}
