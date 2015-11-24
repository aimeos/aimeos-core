<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Common\Item\Type;


/**
 * Test class for \Aimeos\MShop\Common\Item\Type\Standard.
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
			'common.type.id'   => 1,
			'common.type.code' => 'code',
			'common.type.domain' => 'domain',
			'common.type.label' => 'label',
			'common.type.status' => 1,
			'common.type.siteid' => 1,
			'common.type.mtime' => '2011-01-01 00:00:02',
			'common.type.ctime' => '2011-01-01 00:00:01',
			'common.type.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Common\Item\Type\Standard( 'common.type.', $this->values );
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

	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'code', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'code' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'code', $this->object->getCode() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'domain', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->object->setDomain( 'domain' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'domain', $this->object->getDomain() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'label', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'label' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'label', $this->object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 1 );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
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
		$this->assertEquals( 'common/type', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Common\Item\Type\Standard( 'common.type.' );

		$list = array(
			'common.type.id' => 8,
			'common.type.code' => 'test',
			'common.type.domain' => 'testDomain',
			'common.type.label' => 'test item',
			'common.type.status' => 1,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['common.type.id'], $item->getId() );
		$this->assertEquals( $list['common.type.code'], $item->getCode() );
		$this->assertEquals( $list['common.type.domain'], $item->getDomain() );
		$this->assertEquals( $list['common.type.label'], $item->getLabel() );
		$this->assertEquals( $list['common.type.status'], $item->getStatus() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['common.type.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['common.type.code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['common.type.domain'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['common.type.label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['common.type.status'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['common.type.siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['common.type.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['common.type.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['common.type.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
