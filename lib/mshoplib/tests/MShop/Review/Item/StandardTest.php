<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Review\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'review.id' => 1,
			'review.siteid' => 99,
			'review.domain' => 'product',
			'review.refid' => 'abc-123',
			'review.customerid' => '789',
			'review.orderproductid' => 'xyz456',
			'review.response' => 'test response',
			'review.comment' => 'test comment',
			'review.name' => 'test user',
			'review.rating' => 5,
			'review.status' => 1,
			'review.ctime' => '2020-01-19 17:04:32',
			'review.mtime' => '2020-01-19 18:04:32',
			'review.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Review\Item\Standard( $this->values );
	}


	protected function tearDown() : void
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

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( '1', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '99', $this->object->getSiteId() );
	}


	public function testGetComment()
	{
		$this->assertEquals( 'test comment', $this->object->getComment() );
	}


	public function testSetComment()
	{
		$return = $this->object->setComment( '<span>edit comment</span>' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'edit comment', $this->object->getComment() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCustomerId()
	{
		$this->assertEquals( '789', $this->object->getCustomerId() );
	}


	public function testSetCustomerId()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setCustomerId( '147' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( '147', $this->object->getCustomerId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setDomain( 'customer' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'customer', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'test user', $this->object->getName() );
	}


	public function testSetName()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setName( '<span>test reviewer' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'test reviewer', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetOrderProductId()
	{
		$this->assertEquals( 'xyz456', $this->object->getOrderProductId() );
	}


	public function testSetOrderProductId()
	{
		$return = $this->object->setOrderProductId( 'xyz-456' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'xyz-456', $this->object->getOrderProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRating()
	{
		$this->assertEquals( 5, $this->object->getRating() );
	}


	public function testSetRating()
	{
		$return = $this->object->setRating( 3 );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 3, $this->object->getRating() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetRatingMinMax()
	{
		$this->assertEquals( 0, $this->object->setRating( -1 )->getRating() );
		$this->assertEquals( 5, $this->object->setRating( 6 )->getRating() );
	}


	public function testGetRefId()
	{
		$this->assertEquals( 'abc-123', $this->object->getRefId() );
	}


	public function testSetRefId()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setRefId( 'abc123' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'abc123', $this->object->getRefId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResponse()
	{
		$this->assertEquals( 'test response', $this->object->getResponse() );
	}


	public function testSetResponse()
	{
		$return = $this->object->setResponse( 'edit response</span>' );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( 'edit response', $this->object->getResponse() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( -1 );

		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $return );
		$this->assertEquals( -1, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2020-01-19 18:04:32', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2020-01-19 17:04:32', $this->object->getTimeCreated() );
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
		$this->object->setComment( 'reedit review' );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'review', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Review\Item\Standard();

		$list = $entries = array(
			'review.id' => 1,
			'review.domain' => 'product',
			'review.refid' => 'abc-123',
			'review.customerid' => '789',
			'review.orderproductid' => 'xyz456',
			'review.response' => 'test response',
			'review.comment' => 'test comment',
			'review.name' => 'test user',
			'review.rating' => 5,
			'review.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( $list['review.id'], $item->getId() );
		$this->assertEquals( $list['review.refid'], $item->getRefId() );
		$this->assertEquals( $list['review.domain'], $item->getDomain() );
		$this->assertEquals( $list['review.customerid'], $item->getCustomerId() );
		$this->assertEquals( $list['review.orderproductid'], $item->getOrderProductId() );
		$this->assertEquals( $list['review.response'], $item->getResponse() );
		$this->assertEquals( $list['review.comment'], $item->getComment() );
		$this->assertEquals( $list['review.rating'], $item->getRating() );
		$this->assertEquals( $list['review.status'], $item->getStatus() );
		$this->assertEquals( $list['review.name'], $item->getName() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['review.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['review.siteid'] );
		$this->assertEquals( $this->object->getRefId(), $list['review.refid'] );
		$this->assertEquals( $this->object->getDomain(), $list['review.domain'] );
		$this->assertEquals( $this->object->getCustomerId(), $list['review.customerid'] );
		$this->assertEquals( $this->object->getOrderProductId(), $list['review.orderproductid'] );
		$this->assertEquals( $this->object->getResponse(), $list['review.response'] );
		$this->assertEquals( $this->object->getComment(), $list['review.comment'] );
		$this->assertEquals( $this->object->getName(), $list['review.name'] );
		$this->assertEquals( $this->object->getRating(), $list['review.rating'] );
		$this->assertEquals( $this->object->getStatus(), $list['review.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['review.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['review.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $list['review.editor'] );

		$list = $this->object->toArray();
		$this->assertEquals( $this->object->getTimeCreated(), $list['review.ctime'] );
	}
}
