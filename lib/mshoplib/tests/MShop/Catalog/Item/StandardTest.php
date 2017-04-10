<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MShop\Catalog\Item;


/**
 * Test class for \Aimeos\MShop\Catalog\Item\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $node;
	private $object;
	private $values;
	private $listItems;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$listValues = array( 'id' => 1, 'type' => 'default', 'domain' => 'text' );
		$this->listItems = array( 1 => new \Aimeos\MShop\Common\Item\Lists\Standard( 'catalog.lists.', $listValues ) );

		$this->values = array(
			'id' => 2,
			'code' => 'unit-test',
			'label' => 'unittest',
			'config' => array( 'testcategory' => '10' ),
			'status' => 1,
			'siteid' => '99',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
			'hasChildren' => true
		);

		$this->node = new \Aimeos\MW\Tree\Node\Standard( $this->values );
		$child = new \Aimeos\MShop\Catalog\Item\Standard( $this->node );

		$this->object = new \Aimeos\MShop\Catalog\Item\Standard( $this->node, array( $child ), $this->listItems );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
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

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unit-test', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'unit test' );

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( 'unit test', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unit test' );

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( 'unit test', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'testcategory' => '10' ), $this->object->getConfig() );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'unitcategory' => '12' ) );

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( 0, $this->object->getStatus() );
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

		$list = array(
			'catalog.id' => 1,
			'catalog.code' => 'test',
			'catalog.config' => array( 'test' ),
			'catalog.label' => 'test item',
			'catalog.status' => '0',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['catalog.id'], $item->getId() );
		$this->assertEquals( $list['catalog.code'], $item->getCode() );
		$this->assertEquals( $list['catalog.config'], $item->getConfig() );
		$this->assertEquals( $list['catalog.status'], $item->getStatus() );
		$this->assertEquals( $list['catalog.label'], $item->getLabel() );
	}


	public function testToArray()
	{
		$values = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $values ) );

		$this->assertEquals( $this->values['id'], $values['catalog.id'] );
		$this->assertEquals( $this->values['label'], $values['catalog.label'] );
		$this->assertEquals( $this->values['config'], $values['catalog.config'] );
		$this->assertEquals( $this->values['status'], $values['catalog.status'] );
		$this->assertEquals( $this->values['siteid'], $values['catalog.siteid'] );
		$this->assertEquals( $this->values['code'], $values['catalog.code'] );
		$this->assertEquals( $this->values['ctime'], $values['catalog.ctime'] );
		$this->assertEquals( $this->values['mtime'], $values['catalog.mtime'] );
		$this->assertEquals( $this->values['editor'], $values['catalog.editor'] );
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

		foreach( $children as $child ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Catalog\\Item\\Iface', $child );
		}
	}


	public function testAddChild()
	{
		$return = $this->object->addChild( $this->object );

		$this->assertInstanceOf( '\Aimeos\MShop\Catalog\Item\Iface', $return );
		$this->assertEquals( 2, count( $this->object->getChildren() ) );
	}


	public function testGetChild()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Catalog\\Item\\Iface', $this->object->getChild( 0 ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Catalog\\Exception' );
		$this->object->getChild( 1 );
	}


	public function testGetNode()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Tree\\Node\\Iface', $this->object->getNode() );
	}
}
