<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */


namespace Aimeos\MShop\Product\Manager\Property;


/**
 * Test class for \Aimeos\MShop\Product\Manager\Property\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Product\Manager\Property\Standard( \TestHelperMShop::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Product\\Item\\Property\\Iface', $item );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Product\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.editor', $this->editor ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new \RuntimeException( 'No property item found' );
		}

		$item->setId(null);
		$item->setLanguageId( 'en' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 'unittest' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'product.property.value', '25.0'),
			$search->compare( '==', 'product.property.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ($expected = reset($results)) === false ) {
			throw new \RuntimeException( sprintf( 'No product property item found for value "%1$s".', '25.0' ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertNotEquals( '', $actual->getTypeName() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product/property', $result );
		$this->assertContains( 'product/property/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'product.property.id', null );
		$expr[] = $search->compare( '!=', 'product.property.parentid', null );
		$expr[] = $search->compare( '!=', 'product.property.siteid', null );
		$expr[] = $search->compare( '!=', 'product.property.typeid', null );
		$expr[] = $search->compare( '==', 'product.property.languageid', null );
		$expr[] = $search->compare( '==', 'product.property.value', '25.00' );
		$expr[] = $search->compare( '==', 'product.property.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.property.type.id', null );
		$expr[] = $search->compare( '!=', 'product.property.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.property.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.property.type.code', 'package-length' );
		$expr[] = $search->compare( '>', 'product.property.type.label', '' );
		$expr[] = $search->compare( '==', 'product.property.type.status', 1 );
		$expr[] = $search->compare( '>=', 'product.property.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.property.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.property.type.editor', $this->editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '=~', 'product.property.type.code', 'package-' ),
			$search->compare( '==', 'product.property.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$items = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 8, $total );

		foreach($items as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager('type') );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager('type', 'Standard') );

		$this->setExpectedException('\\Aimeos\\MShop\\Exception');
		$this->object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('\\Aimeos\\MShop\\Exception');
		$this->object->getSubManager('type', 'unknown');
	}
}
