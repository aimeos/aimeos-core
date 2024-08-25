<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Locale\Manager\Site;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Locale\Manager\Site\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $this->object->create() );
	}


	public function testSaveIdException()
	{
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$this->object->save( $this->object->create() );
	}


	public function testSaveUpdateDelete()
	{
		$item = $this->object->create();
		$item->setLabel( 'new name' );
		$item->setStatus( 1 );
		$item->setCode( 'xx' );
		$resultSaved = $this->object->insert( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'new new name' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelper::context();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getChildren(), $itemSaved->getChildren() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getTheme(), $itemSaved->getTheme() );
		$this->assertEquals( $item->getLogos(), $itemSaved->getLogos() );
		$this->assertEquals( $item->getLogo(), $itemSaved->getLogo() );
		$this->assertEquals( $item->getIcon(), $itemSaved->getIcon() );

		$this->assertEquals( $context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getChildren(), $itemUpd->getChildren() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getTheme(), $itemUpd->getTheme() );
		$this->assertEquals( $itemExp->getLogos(), $itemUpd->getLogos() );
		$this->assertEquals( $itemExp->getLogo(), $itemUpd->getLogo() );
		$this->assertEquals( $itemExp->getIcon(), $itemUpd->getIcon() );

		$this->assertEquals( $context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testFind()
	{
		$item = $this->object->find( 'unittest' );

		$this->assertEquals( 'unittest', $item->getCode() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$a = $this->object->search( $search )->toArray();
		if( ( $expected = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Site item not found' );
		}

		$actual = $this->object->get( $expected->getId() );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $actual );
		$this->assertEquals( $expected, $actual );
	}


	public function testRate()
	{
		$item = $this->object->find( 'unittest' );
		$result = $this->object->rate( $item->getId(), '4.00', 5 );
		$item2 = $this->object->find( 'unittest' );

		$this->object->rate( $item->getId(), $item->getRating(), $item->getRatings() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $result );
		$this->assertEquals( '4.00', $item2->getRating() );
		$this->assertEquals( 5, $item2->getRatings() );
	}


	public function testSearch()
	{
		$siteid = $this->context->locale()->getSiteId();

		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'locale.site.id', null );
		$expr[] = $search->compare( '==', 'locale.site.siteid', $siteid );
		$expr[] = $search->compare( '==', 'locale.site.code', 'unittest' );
		$expr[] = $search->compare( '==', 'locale.site.label', 'Unit test site' );
		$expr[] = $search->compare( '=~', 'locale.site.config', '{' );
		$expr[] = $search->compare( '==', 'locale.site.icon', 'path/to/site-icon.png' );
		$expr[] = $search->compare( '=~', 'locale.site.logo', '{' );
		$expr[] = $search->compare( '==', 'locale.site.rating', '0.00' );
		$expr[] = $search->compare( '==', 'locale.site.ratings', 0 );
		$expr[] = $search->compare( '==', 'locale.site.refid', '1234' );
		$expr[] = $search->compare( '==', 'locale.site.theme', 'shop' );
		$expr[] = $search->compare( '==', 'locale.site.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.editor', '' );

		$total = 0;
		$search->setConditions( $search->and( $expr ) )->order( 'locale.site.position' );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria and total
		$search = $this->object->filter( true );
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest' ) ) );
		$search->slice( 0, 1 );

		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertGreaterThanOrEqual( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'locale/site', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetPath()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$list = $this->object->getPath( $expected->getId() )->toArray();
		$this->assertArrayHasKey( $expected->getId(), $list );
		$this->assertEquals( $expected, $list[$expected->getId()] );
	}


	public function testGetTree()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$expected = $this->object->search( $search )->first();

		$item = $this->object->getTree( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetTreeNoId()
	{
		$object = $this->getMockBuilder( \Aimeos\MShop\Locale\Manager\Site\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$object->create()] ) );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $object->getTree() );
	}


	public function testGetTreeNoItem()
	{
		$object = $this->getMockBuilder( \Aimeos\MShop\Locale\Manager\Site\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'search' )
			->willReturn( map() );

		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$object->getTree();
	}


	public function testGetTreeCache()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item = $this->object->getTree( $expected->getId() );
		$item2 = $this->object->getTree( $expected->getId() );

		$this->assertSame( $item, $item2 );
	}


	public function testGetTreeDefault()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'default' ) );
		$results = $this->object->search( $search )->toArray();

		$this->assertIsArray( $results );

		if( ( $expected = reset( $results ) ) !== false )
		{
			$item = $this->object->getTree();
			$this->assertEquals( $expected, $item );
		}
	}


	public function testMoveItem()
	{
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$this->object->move( '', '', '' );
	}
}
