<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Manager;


/**
 * Test class for \Aimeos\MShop\Service\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Service\Manager\Standard( \TestHelperMShop::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Service\\Item\\Iface', $this->object->createItem() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Service\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitcode' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No service provider item found.' );
		}

		$item->setId( null );
		$item->setCode( 'newstaticdelivery' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( '2ndChang' );
		$itemExp->setLabel( '2ndNameChanged' );
		$itemExp->setPosition( '1' );
		$itemExp->setStatus( '1' );
		$itemExp->setProvider( 'HS' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'unitcode' );

		$this->assertEquals( 'unitcode', $item->getCode() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitcode' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId(), array( 'text' ) ) );
		$this->assertEquals( 2, count( $item->getRefItems( 'text' ) ) );
		$this->assertNotEquals( '', $item->getTypeName() );
	}


	public function testSearchItem()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'service.id', null );
		$expr[] = $search->compare( '!=', 'service.siteid', null );
		$expr[] = $search->compare( '>', 'service.typeid', 0 );
		$expr[] = $search->compare( '>=', 'service.position', 0 );
		$expr[] = $search->compare( '==', 'service.code', 'unitcode' );
		$expr[] = $search->compare( '==', 'service.label', 'unitlabel' );
		$expr[] = $search->compare( '==', 'service.provider', 'Standard' );
		$expr[] = $search->compare( '~=', 'service.config', 'url' );
		$expr[] = $search->compare( '==', 'service.status', 1 );
		$expr[] = $search->compare( '>=', 'service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'service.type.id', null );
		$expr[] = $search->compare( '!=', 'service.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.type.code', 'delivery' );
		$expr[] = $search->compare( '==', 'service.type.domain', 'service' );
		$expr[] = $search->compare( '==', 'service.type.label', 'Delivery' );
		$expr[] = $search->compare( '==', 'service.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'service.lists.id', null );
		$expr[] = $search->compare( '!=', 'service.lists.siteid', null );
		$expr[] = $search->compare( '>', 'service.lists.parentid', 0 );
		$expr[] = $search->compare( '==', 'service.lists.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.lists.typeid', 0 );
		$expr[] = $search->compare( '>', 'service.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'service.lists.datestart', null );
		$expr[] = $search->compare( '==', 'service.lists.dateend', null );
		$expr[] = $search->compare( '!=', 'service.lists.config', null );
		$expr[] = $search->compare( '==', 'service.lists.position', 0 );
		$expr[] = $search->compare( '==', 'service.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'service.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'service.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'service.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.lists.type.code', 'unittype1' );
		$expr[] = $search->compare( '==', 'service.lists.type.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.lists.type.label', '' );
		$expr[] = $search->compare( '==', 'service.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.lists.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'service.provider', 'unitprovider' ),
			$search->compare( '==', 'service.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 0, count( $this->object->searchItems( $search ) ) );
	}


	public function testGetProvider()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.type.code', 'delivery' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$result = $this->object->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No service item found' );
		}

		$item->setProvider( 'Standard,Example' );
		$provider = $this->object->getProvider( $item );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Service\\Provider\\Iface', $provider );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Example', $provider );


		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getProvider( $this->object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'service', $result );
		$this->assertContains( 'service/type', $result );
		$this->assertContains( 'service/lists', $result );
		$this->assertContains( 'service/lists/type', $result );
	}


	public function testGetSearchAttributes()
	{
		$attribs = $this->object->getSearchAttributes();
		foreach( $attribs as $obj ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $obj );
		}

	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $search );
	}
}
