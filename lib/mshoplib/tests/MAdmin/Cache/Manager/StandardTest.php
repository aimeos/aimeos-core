<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MAdmin\Cache\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MAdmin\Cache\Manager\Standard( $this->context );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MAdmin\\Cache\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'cache', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->object->searchItems( $search );

		$this->assertEquals( 1, count( $results ) );

		foreach( $results as $id => $item )
		{
			$this->assertEquals( 'unittest', $id );
			$this->assertEquals( 'unittest', $item->getId() );
			$this->assertEquals( $siteid, $item->getSiteId() );
			$this->assertEquals( 'unit test value', $item->getValue() );
		}
	}


	public function testGetItem()
	{
		$siteid = $this->context->getLocale()->getSiteId();
		$item = $this->object->getItem( 'unittest' );

		$this->assertEquals( 'unittest', $item->getId() );
		$this->assertEquals( $siteid, $item->getSiteId() );
		$this->assertEquals( 'unit test value', $item->getValue() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$item->setId( 'unittest2' );
		$item->setValue( 'test2' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 'test3' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $item->getId() );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 'unittest2', $item->getId() );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTimeExpire(), $itemSaved->getTimeExpire() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getTags(), $itemSaved->getTags() );

		$this->assertEquals( $itemExp->getId(), $itemSaved->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $itemExp->getTimeExpire(), $itemUpd->getTimeExpire() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getTags(), $itemUpd->getTags() );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $resultSaved );
		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $resultUpd );

		$this->setExpectedException( '\\Aimeos\\MAdmin\\Cache\\Exception' );
		$this->object->getItem( $item->getId() );
	}
}
