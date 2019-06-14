<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Price\Manager\Property\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$manager = \Aimeos\MShop\Price\Manager\Factory::create( \TestHelperMShop::getContext() );
		$this->object = $manager->getSubManager( 'property' )->getSubManager('type');
	}


	protected function tearDown()
	{
		unset($this->object);
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->cleanup( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'price/property/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $price ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $price );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'price.property.type.code', 'taxrate-local' ),
			$search->compare( '==', 'price.property.type.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$results = $this->object->searchItems( $search );

		if( ($expected = reset($results) ) === false )
		{
			throw new \RuntimeException( 'No property type item found.' );
		}

		$actual = $this->object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( \Aimeos\MW\Common\Exception::class );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.property.type.editor', $this->editor ) );
		$results = $this->object->searchItems($search);

		if( ( $item = reset($results) ) === false ) {
			throw new \RuntimeException( 'No type item found' );
		}

		$item->setId(null);
		$item->setCode( 'unitTestSave' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
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
		$expr[] = $search->compare( '!=', 'price.property.type.id', null );
		$expr[] = $search->compare( '!=', 'price.property.type.siteid', null );
		$expr[] = $search->compare( '==', 'price.property.type.domain', 'price' );
		$expr[] = $search->compare( '==', 'price.property.type.code', 'taxrate-local' );
		$expr[] = $search->compare( '==', 'price.property.type.label', 'Local tax' );
		$expr[] = $search->compare( '>=', 'price.property.type.position', 0 );
		$expr[] = $search->compare( '==', 'price.property.type.status', 1 );
		$expr[] = $search->compare( '>=', 'price.property.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.property.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.property.type.editor', $this->editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager('unknown');
	}
}
