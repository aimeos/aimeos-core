<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Text\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Text\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$textItem = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $textItem );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['text.type' => 'name'] );
		$this->assertEquals( 'name', $item->getType() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'text', $result );
		$this->assertContains( 'text/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'text.label', 'service_text3.1' ) );

		$item = $this->object->search( $search, ['customer'] )->first( new \RuntimeException( 'No item found' ) );
		$listItem = $item->getListItems( 'customer', 'test' )->first( new \RuntimeException( 'No list item found' ) );

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'text.id', null );
		$expr[] = $search->compare( '!=', 'text.siteid', null );
		$expr[] = $search->compare( '==', 'text.languageid', 'de' );
		$expr[] = $search->compare( '==', 'text.type', 'serviceinformation' );
		$expr[] = $search->compare( '==', 'text.label', 'service_text3.1' );
		$expr[] = $search->compare( '==', 'text.domain', 'service' );
		$expr[] = $search->compare( '=~', 'text.content', 'Unittest' );
		$expr[] = $search->compare( '==', 'text.status', 0 );
		$expr[] = $search->compare( '>=', 'text.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.editor', $this->editor );

		$param = ['customer', 'test', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'text:has', $param ), null );

		$param = ['customer', 'test'];
		$expr[] = $search->compare( '!=', $search->make( 'text:has', $param ), null );

		$param = ['customer'];
		$expr[] = $search->compare( '!=', $search->make( 'text:has', $param ), null );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsAll()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'text.editor', $this->editor ) );
		$this->assertEquals( 93, count( $this->object->search( $search )->toArray() ) );
	}


	public function testSearchItemsBase()
	{
		$total = 0;
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '==', 'text.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 5 );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 90, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '~=', 'text.content', 'Monetary' ),
			$search->compare( '==', 'text.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );

		$result = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No text item including "%1$s" found', 'Monetary' ) );
		}


		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'text.content', 'Cafe Noire Expresso' ),
			$search->compare( '==', 'text.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );

		$a = $this->object->search( $search )->toArray();

		if( ( $item = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Text item not found.' );
		}

		$item->setId( null );
		$item->setLabel( 'Cafe Noire Unittest' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'Cafe Noire Expresso x' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getContent(), $itemSaved->getContent() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getContent(), $itemUpd->getContent() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
