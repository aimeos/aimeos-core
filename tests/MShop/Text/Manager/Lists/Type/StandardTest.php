<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Text\Manager\Lists\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->object = new \Aimeos\MShop\Text\Manager\Lists\Type\Standard( \TestHelper::context() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'text/lists/type', $result );
	}


	public function testCreate()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $item );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'text.lists.type.editor', $this->editor ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No text list type item found' );
		}

		$this->assertEquals( $expected, $this->object->get( $expected->getId() ) );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'text.lists.type.editor', $this->editor ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestSave' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testSearch()
	{
		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'text.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'text.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'text.lists.type.code', 'default' );
		$expr[] = $search->compare( '==', 'text.lists.type.domain', 'media' );
		$expr[] = $search->compare( '>', 'text.lists.type.label', '' );
		$expr[] = $search->compare( '>=', 'text.lists.type.position', 0 );
		$expr[] = $search->compare( '==', 'text.lists.type.status', 1 );
		$expr[] = $search->compare( '==', 'text.lists.type.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );

		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchSlice()
	{
		$total = 0;
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'text.lists.type.code', 'default' ),
			$search->compare( '==', 'text.lists.type.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->setSortations( [$search->sort( '-', 'text.lists.type.position' )] );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 7, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

}
