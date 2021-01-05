<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MAdmin\Cache\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MAdmin\Cache\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Cache\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Cache\Item\Iface::class, $this->object->create() );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Cache\Manager\Iface::class, $this->object->delete( [-1] ) );
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
		$this->object->save( $this->object->create()->setId( 'unittest' )->setValue( 'test' ) );

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->object->search( $search )->toArray();

		$this->assertEquals( 1, count( $results ) );

		foreach( $results as $id => $item )
		{
			$this->assertEquals( 'unittest', $id );
			$this->assertEquals( 'unittest', $item->getId() );
			$this->assertEquals( 'test', $item->getValue() );
		}

		$this->object->delete( 'unittest' );
	}


	public function testGetItem()
	{
		$this->object->save( $this->object->create()->setId( 'unittest' )->setValue( 'test' ) );

		$item = $this->object->get( 'unittest' );

		$this->assertEquals( 'unittest', $item->getId() );
		$this->assertEquals( 'test', $item->getValue() );

		$this->object->delete( 'unittest' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->create();
		$item->setId( 'unittest1' );
		$item->setValue( 'test2' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 'test3' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $item->getId() );

		$this->object->delete( $item->getId() );

		$this->assertEquals( 'unittest1', $item->getId() );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getTimeExpire(), $itemSaved->getTimeExpire() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getTags(), $itemSaved->getTags() );

		$this->assertEquals( $itemExp->getId(), $itemSaved->getId() );
		$this->assertEquals( $itemExp->getTimeExpire(), $itemUpd->getTimeExpire() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getTags(), $itemUpd->getTags() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MAdmin\Cache\Exception::class );
		$this->object->get( $item->getId() );
	}
}
