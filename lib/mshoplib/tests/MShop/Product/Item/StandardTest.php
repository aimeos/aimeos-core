<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Product\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'product.id' => 1,
			'product.siteid' => 99,
			'product.type' => 'test',
			'product.status' => 1,
			'product.code' => 'TEST',
			'product.label' => 'testproduct',
			'product.config' => array( 'css-class' => 'test' ),
			'product.datestart' => null,
			'product.dateend' => null,
			'product.ctime' => '2011-01-19 17:04:32',
			'product.mtime' => '2011-01-19 18:04:32',
			'product.editor' => 'unitTestUser',
			'product.target' => 'testtarget',
		);

		$propItems = array(
			2 => new \Aimeos\MShop\Common\Item\Property\Standard( 'product.property.', array(
				'product.property.id' => 2,
				'product.property.parentid' => 1,
				'product.property.type' => 'proptest',
				'product.property.languageid' => 'de',
				'languageid' => 'de',
			) ),
			3 => new \Aimeos\MShop\Common\Item\Property\Standard( 'product.property.', array(
				'product.property.id' => 3,
				'product.property.parentid' => 1,
				'product.property.type' => 'proptype',
				'product.property.languageid' => 'de',
				'languageid' => 'fr',
			) ),
		);

		$this->object = new \Aimeos\MShop\Product\Item\Standard( $this->values, [], [], $propItems );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->values );
	}


	public function testGetId()
	{
		$this->assertEquals( '1', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '1', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->setId( 2 );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetPropertyItems()
	{
		$propItems = $this->object->getPropertyItems();

		$this->assertEquals( 1, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testGetPropertyItemsAll()
	{
		$propItems = $this->object->getPropertyItems( null, false );

		$this->assertEquals( 2, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testGetPropertyItemsType()
	{
		$propItems = $this->object->getPropertyItems( 'proptest' );

		$this->assertEquals( 1, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testGetType()
	{
		$this->assertEquals( 'test', $this->object->getType() );
	}


	public function testSetType()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setType( 'default' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'default', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'TEST', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setCode( 'NEU' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'NEU', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 8 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 8, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testproduct', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'editproduct' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'editproduct', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'css-class' => 'test' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( 'test', $this->object->getConfigValue( 'css-class' ) );
	}


	public function testSetConfig()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setConfig( array( 'key' => 'value' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( array( 'key' => 'value' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'testtarget', $this->object->getTarget() );
	}


	public function testSetTarget()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setTarget( 'ttarget' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'ttarget', $this->object->getTarget() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( null, $this->object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( null, $this->object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-19 18:04:32', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-19 17:04:32', $this->object->getTimeCreated() );
	}


	public function testSetTimeCreated()
	{
		$return = $this->object->setTimeCreated( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getTimeCreated() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
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


	public function testIsModifiedTrue()
	{
		$this->object->setLabel( 'reeditProduct' );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'product', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Product\Item\Standard();

		$list = $entries = array(
			'product.id' => 1,
			'product.type' => 'test',
			'product.label' => 'test item',
			'product.code' => 'test',
			'product.datestart' => '2000-01-01 00:00:00',
			'product.dateend' => '2001-01-01 00:00:00',
			'product.config' => array( 'key' => 'value' ),
			'product.status' => 0,
			'product.target' => 'ttarget',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['product.id'], $item->getId() );
		$this->assertEquals( $list['product.code'], $item->getCode() );
		$this->assertEquals( $list['product.label'], $item->getLabel() );
		$this->assertEquals( $list['product.type'], $item->getType() );
		$this->assertEquals( $list['product.datestart'], $item->getDateStart() );
		$this->assertEquals( $list['product.dateend'], $item->getDateEnd() );
		$this->assertEquals( $list['product.config'], $item->getConfig() );
		$this->assertEquals( $list['product.status'], $item->getStatus() );
		$this->assertEquals( $list['product.target'], $item->getTarget() );
		$this->assertNull( $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.siteid'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['product.code'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['product.label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['product.status'] );
		$this->assertEquals( $this->object->getDateStart(), $arrayObject['product.datestart'] );
		$this->assertEquals( $this->object->getDateEnd(), $arrayObject['product.dateend'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['product.config'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.editor'] );
		$this->assertEquals( $this->object->getTarget(), $arrayObject['product.target'] );
	}

}
