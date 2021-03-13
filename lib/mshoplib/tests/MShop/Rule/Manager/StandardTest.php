<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Rule\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $exampleRule;
	private $exampleRule2;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Rule\Manager\Factory::create( \TestHelperMShop::getContext() );

		$this->exampleRule = $this->object->create();
		$this->exampleRule->setType( 'catalog' );
		$this->exampleRule->setProvider( 'Percent' );
		$this->exampleRule->setConfig( ['percent' => '10'] );

		$this->exampleRule2 = clone $this->exampleRule;
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

		$this->assertContains( 'rule', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $this->object->create() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['rule.type' => 'catalog'] );
		$this->assertEquals( 'catalog', $item->getType() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 )
			->add( ['rule.provider' => 'Percent,Category', 'rule.editor' => $this->editor] );

		$expected = $this->object->search( $search )
			->first( new \RuntimeException( sprintf( 'No rule item including "%1$s" found', 'Percent,Category' ) ) );

		$actual = $this->object->get( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter()->slice( 0, 1 )
			->add( ['rule.provider' => 'Percent,Category', 'rule.editor' => $this->editor] );

		$item = $this->object->search( $search )
			->first( new \RuntimeException( sprintf( 'No rule item including "%1$s" found', 'Percent,Category' ) ) );

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
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
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
		$expr[] = $search->compare( '!=', 'rule.id', null );
		$expr[] = $search->compare( '!=', 'rule.siteid', null );
		$expr[] = $search->compare( '==', 'rule.type', 'catalog' );
		$expr[] = $search->compare( '!=', 'rule.label', null );
		$expr[] = $search->compare( '~=', 'rule.provider', 'Percent,Category' );
		$expr[] = $search->compare( '>=', 'rule.datestart', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'rule.dateend', null );
		$expr[] = $search->compare( '~=', 'rule.config', 'home' );
		$expr[] = $search->compare( '==', 'rule.position', 0 );
		$expr[] = $search->compare( '==', 'rule.status', 1 );
		$expr[] = $search->compare( '>=', 'rule.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'rule.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'rule.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$this->assertEquals( 1, $this->object->search( $search, [], $total )->count() );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->filter( true )->slice( 0, 1 );
		$results = $this->object->search( $search, [] );
		$this->assertEquals( 1, count( $results ) );

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
