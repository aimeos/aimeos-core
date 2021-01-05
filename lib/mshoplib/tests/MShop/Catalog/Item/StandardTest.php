<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Catalog\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $node;
	private $object;
	private $values;
	private $listItems;


	protected function setUp() : void
	{
		$listValues = ['id' => 1, 'type' => 'default', 'domain' => 'text'];
		$this->listItems = ['text' => [1 => new \Aimeos\MShop\Common\Item\Lists\Standard( 'catalog.lists.', $listValues )]];

		$this->values = array(
			'id' => 2,
			'parentid' => 3,
			'level' => 1,
			'code' => 'unit-test',
			'label' => 'unittest',
			'url' => 'unit_test',
			'config' => ['testcategory' => '10'],
			'status' => 1,
			'siteid' => '99',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
			'target' => 'testtarget',
			'hasChildren' => true
		);

		$this->node = new \Aimeos\MW\Tree\Node\Standard( $this->values );
		$childnode = new \Aimeos\MW\Tree\Node\Standard( array_merge( $this->values, ['id' => 3] ) );

		$child = new \Aimeos\MShop\Catalog\Item\Standard( $childnode );
		$this->object = new \Aimeos\MShop\Catalog\Item\Standard( $this->node, array( $child ), $this->listItems );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 2, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 3, $this->object->getParentId() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unit-test', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'unit test' );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 'unit test', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLevel()
	{
		$this->assertEquals( 1, $this->object->getLevel() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unit test' );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 'unit test', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'unit_test', $this->object->getUrl() );
	}


	public function testSetUrl()
	{
		$return = $this->object->setUrl( 'summer_shirts' );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 'summer_shirts', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'testcategory' => '10' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( '10', $this->object->getConfigValue( 'testcategory' ) );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'unitcategory' => '12' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( array( 'unitcategory' => '12' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'testtarget', $this->object->getTarget() );
	}


	public function testSetTarget()
	{
		$return = $this->object->setTarget( 'ttarget' );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 'ttarget', $this->object->getTarget() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteid()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'catalog', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Catalog\Item\Standard( new \Aimeos\MW\Tree\Node\Standard() );

		$list = $entries = array(
			'catalog.id' => 1,
			'catalog.code' => 'test',
			'catalog.config' => array( 'test' ),
			'catalog.label' => 'test item',
			'catalog.status' => '0',
			'catalog.target' => 'ttarget',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );

		$this->assertEquals( $list['catalog.id'], $item->getId() );
		$this->assertEquals( $list['catalog.code'], $item->getCode() );
		$this->assertEquals( $list['catalog.config'], $item->getConfig() );
		$this->assertEquals( $list['catalog.status'], $item->getStatus() );
		$this->assertEquals( $list['catalog.label'], $item->getLabel() );
		$this->assertEquals( $list['catalog.target'], $item->getTarget() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$values = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $values ) );

		$this->assertEquals( $this->values['id'], $values['catalog.id'] );
		$this->assertEquals( $this->values['level'], $values['catalog.level'] );
		$this->assertEquals( $this->values['parentid'], $values['catalog.parentid'] );
		$this->assertEquals( $this->values['label'], $values['catalog.label'] );
		$this->assertEquals( $this->values['config'], $values['catalog.config'] );
		$this->assertEquals( $this->values['status'], $values['catalog.status'] );
		$this->assertEquals( $this->values['siteid'], $values['catalog.siteid'] );
		$this->assertEquals( $this->values['code'], $values['catalog.code'] );
		$this->assertEquals( $this->values['ctime'], $values['catalog.ctime'] );
		$this->assertEquals( $this->values['mtime'], $values['catalog.mtime'] );
		$this->assertEquals( $this->values['editor'], $values['catalog.editor'] );
		$this->assertEquals( $this->values['target'], $values['catalog.target'] );
		$this->assertEquals( $this->values['hasChildren'], $values['catalog.hasChildren'] );
	}


	public function testHasChildren()
	{
		$this->assertTrue( $this->object->hasChildren() );
	}


	public function testGetChildren()
	{
		$children = $this->object->getChildren();
		$this->assertEquals( 1, count( $children ) );
		$this->assertInstanceOf( \Aimeos\Map::class, $children );

		foreach( $children as $child ) {
			$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $child );
		}
	}


	public function testGetChild()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->getChild( 0 ) );

		$this->expectException( \Aimeos\MShop\Catalog\Exception::class );
		$this->object->getChild( 1 );
	}


	public function testAddChild()
	{
		$return = $this->object->addChild( $this->object );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 2, count( $this->object->getChildren() ) );
	}


	public function testDeleteChild()
	{
		$child = $this->object->getChild( 0 );

		$return = $this->object->deleteChild( $child );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $return );
		$this->assertEquals( 0, count( $this->object->getChildren() ) );
	}


	public function testGetChildrenDeleted()
	{
		$child = $this->object->getChild( 0 );
		$this->object->deleteChild( $child );

		$result = $this->object->getChildrenDeleted();

		$this->assertInstanceOf( \Aimeos\Map::class, $result );
		$this->assertEquals( [$child], $result->toArray() );
	}


	public function testGetNode()
	{
		$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $this->object->getNode() );
	}


	public function testToList()
	{
		$list = $this->object->toList();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( '2', $list );
		$this->assertArrayHasKey( '3', $list );
	}
}
