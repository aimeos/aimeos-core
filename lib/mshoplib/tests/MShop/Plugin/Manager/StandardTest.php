<?php

namespace Aimeos\MShop\Plugin\Manager\StandardTest;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */
class Publisher extends \Aimeos\MW\Observer\Publisher\Base
{
}


namespace Aimeos\MShop\Plugin\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $examplePlugin;
	private $examplePlugin2;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Plugin\Manager\Factory::create( \TestHelperMShop::getContext() );

		$this->examplePlugin = $this->object->createItem();
		$this->examplePlugin->setType( 'order' );

		$this->examplePlugin->setProvider( 'Example' );
		$this->examplePlugin->setConfig( array( "limit" => "10" ) );
		$this->examplePlugin->setStatus( 1 );

		$this->examplePlugin2 = clone $this->examplePlugin;
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->cleanup( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->deleteItems( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'plugin', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $this->object->createItem() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->createItem( ['plugin.type' => 'order'] );
		$this->assertEquals( 'order', $item->getType() );
	}


	public function testRegister()
	{
		$publisher = new \Aimeos\MShop\Plugin\Manager\StandardTest\Publisher();
		$this->object->register( $publisher, 'order' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search );
		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No plugin item including "%1$s" found', 'Shipping' ) );
		}

		$actual = $this->object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$a = $this->object->searchItems( $search );
		if( ( $item = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Search provider in test failt' );
		}

		$item->setId( null );
		$item->setProvider( 'Example1' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProvider( 'Example' );
		$itemExp->setPosition( 5 );
		$itemExp->setStatus( -1 );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'plugin.id', null );
		$expr[] = $search->compare( '!=', 'plugin.siteid', null );
		$expr[] = $search->compare( '==', 'plugin.type', 'order' );
		$expr[] = $search->compare( '!=', 'plugin.label', null );
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit' );
		$expr[] = $search->compare( '~=', 'plugin.config', 'single-number-max' );
		$expr[] = $search->compare( '==', 'plugin.position', 0 );
		$expr[] = $search->compare( '==', 'plugin.status', 1 );
		$expr[] = $search->compare( '>=', 'plugin.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'plugin.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsProvider()
	{
		$search = $this->object->createSearch();

		$expr = $conditions = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'Shipping,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'BasketLimits,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );

		//search without base criteria
		$search = $this->object->createSearch();
		$search->setConditions( $search->combine( '||', $conditions ) );
		$this->assertEquals( 3, count( $this->object->searchItems( $search ) ) );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->createSearch( true )->setSlice( 0, 2 );
		$results = $this->object->searchItems( $search, [] );
		$this->assertEquals( 2, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'type', 'unknown' );
	}
}
