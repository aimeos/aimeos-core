<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Coupon\Item;


/**
 * Test class for \Aimeos\MShop\Coupon\Item\Example.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'coupon.siteid' => 123,
			'coupon.label' => 'test coupon',
			'coupon.provider' => 'Example',
			'coupon.config' => array( 'key'=>'test' ),
			'coupon.start' => null,
			'coupon.end' => null,
			'coupon.status' => true,
			'coupon.mtime' => '2011-01-01 00:00:02',
			'coupon.ctime' => '2011-01-01 00:00:01',
			'coupon.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Coupon\Item\Standard( $this->values );
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
		$this->assertNULL( $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( 2 );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertFalse( false, $this->object->isModified() );
		$this->assertEquals( 2, $this->object->getId() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( true, $this->object->isModified() );
		$this->assertEquals( null, $this->object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'test coupon', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unitTest' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( 'unitTest', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateStart()
	{
		$this->assertNull( $this->object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
	}

	public function testGetDateEnd()
	{
		$this->assertNull( $this->object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getDateEnd() );
	}

	public function testGetProvider()
	{
		$this->assertEquals( 'Example', $this->object->getProvider() );
	}

	public function testSetProvider()
	{
		$return = $this->object->setProvider( 'Test' );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( 'Test', $this->object->getProvider() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array( 'key'=>'test' ), $this->object->getConfig() );
	}

	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'value'=>1 ) );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( array( 'value'=>1 ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$return = $this->object->setStatus( 14 );

		$this->assertInstanceOf( '\Aimeos\MShop\Coupon\Item\Iface', $return );
		$this->assertEquals( 14, $this->object->getStatus() );
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


	public function testGetResourceType()
	{
		$this->assertEquals( 'coupon', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Coupon\Item\Standard();

		$list = array(
			'coupon.id' => 1,
			'coupon.config' => array( 'test' ),
			'coupon.label' => 'test item',
			'coupon.provider' => 'test',
			'coupon.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['coupon.id'], $item->getId() );
		$this->assertEquals( $list['coupon.config'], $item->getConfig() );
		$this->assertEquals( $list['coupon.label'], $item->getLabel() );
		$this->assertEquals( $list['coupon.provider'], $item->getProvider() );
		$this->assertEquals( $list['coupon.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( ( count( $this->values ) + 1 ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['coupon.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['coupon.siteid'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['coupon.config'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['coupon.label'] );
		$this->assertEquals( $this->object->getProvider(), $arrayObject['coupon.provider'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['coupon.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['coupon.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['coupon.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['coupon.editor'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
