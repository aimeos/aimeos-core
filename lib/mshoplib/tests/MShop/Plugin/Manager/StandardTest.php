<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $examplePlugin;
	private $examplePlugin2;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Plugin\Manager\Factory::create( \TestHelperMShop::getContext() );

		$this->examplePlugin = $this->object->create();
		$this->examplePlugin->setType( 'order' );

		$this->examplePlugin->setProvider( 'Example' );
		$this->examplePlugin->setConfig( array( "limit" => "10" ) );
		$this->examplePlugin->setStatus( 1 );

		$this->examplePlugin2 = clone $this->examplePlugin;
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'plugin', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $this->object->create() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['plugin.type' => 'order'] );
		$this->assertEquals( 'order', $item->getType() );
	}


	public function testRegister()
	{
		$publisher = new TestPublisher();
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Manager\Iface::class, $this->object->register( $publisher, 'order' ) );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$result = $this->object->search( $search )->toArray();
		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No plugin item including "%1$s" found', 'Shipping' ) );
		}

		$actual = $this->object->get( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );

		$a = $this->object->search( $search )->toArray();
		if( ( $item = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Search provider in test failt' );
		}

		$item->setId( null );
		$item->setProvider( 'Example1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProvider( 'Example' );
		$itemExp->setPosition( 5 );
		$itemExp->setStatus( -1 );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


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

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();

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

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsProvider()
	{
		$search = $this->object->filter();

		$expr = $conditions = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'Shipping,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->and( $expr );
		$expr = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->and( $expr );
		$expr = [];
		$expr[] = $search->compare( '~=', 'plugin.provider', 'BasketLimits,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->and( $expr );

		//search without base criteria
		$search = $this->object->filter();
		$search->setConditions( $search->or( $conditions ) );
		$this->assertEquals( 3, count( $this->object->search( $search )->toArray() ) );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->filter( true )->slice( 0, 2 );
		$results = $this->object->search( $search, [] );
		$this->assertEquals( 2, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'type', 'unknown' );
	}
}


class TestPublisher implements \Aimeos\MW\Observer\Publisher\Iface
{
	use \Aimeos\MW\Observer\Publisher\Traits;
}
