<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

namespace Aimeos\MShop\Locale\Item\Site;


/**
 * Test class for \Aimeos\MShop\Locale\Item\Site\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'id' => 12,
			'siteid' => 12,
			'code' => 'ExtID',
			'label' => 'My Site',
			'config' => array( 'timezone' => 'Europe/Berlin' ),
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$children = array( new \Aimeos\MShop\Locale\Item\Site\Standard() );
		$this->object = new \Aimeos\MShop\Locale\Item\Site\Standard( $this->values, $children );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( 12, $this->object->getId() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 12, $this->object->getSiteId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 12 );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 99 );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'ExtID', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$this->object->setCode( 'OtherExtID' );
		$this->assertEquals( 'OtherExtID', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'My Site', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'Other Name' );
		$this->assertEquals( 'Other Name', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'timezone' => 'Europe/Berlin' ), $this->object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->object->setConfig( array( 'timezone' => 'Europe/Paris' ) );
		$this->assertEquals( array( 'timezone' => 'Europe/Paris' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
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


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Locale\Item\Site\Standard();

		$list = array(
			'locale.site.id' => 2,
			'locale.site.code' => 'test',
			'locale.site.label' => 'test item',
			'locale.site.status' => 1,
			'locale.site.config' => array( 'test' ),
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['locale.site.id'], $item->getId() );
		$this->assertEquals( $list['locale.site.code'], $item->getCode() );
		$this->assertEquals( $list['locale.site.label'], $item->getLabel() );
		$this->assertEquals( $list['locale.site.status'], $item->getStatus() );
		$this->assertEquals( $list['locale.site.config'], $item->getConfig() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.site.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.site.siteid'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['locale.site.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['locale.site.label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.site.status'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['locale.site.config'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.site.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.site.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.site.editor'] );
	}


	public function testAddChild()
	{
		$this->object->addChild( $this->object );
		$this->assertEquals( 2, count( $this->object->getChildren() ) );
	}


	public function testGetChild()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Locale\\Item\\Site\\Iface', $this->object->getChild( 0 ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Locale\\Exception' );
		$this->object->getChild( 1 );
	}


	public function testGetChildren()
	{
		$children = $this->object->getChildren();
		$this->assertEquals( 1, count( $children ) );

		foreach( $children as $child ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Locale\\Item\\Site\\Iface', $child );
		}
	}


	public function testHasChildren()
	{
		$this->assertTrue( $this->object->hasChildren() );
	}
}
