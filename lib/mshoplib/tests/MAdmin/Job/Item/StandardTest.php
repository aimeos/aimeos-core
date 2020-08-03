<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MAdmin\Job\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'job.id' => 1,
			'job.siteid' => 2,
			'job.label' => 'unittest job',
			'job.method' => 'Product_Import_Text.importFile',
			'job.parameter' => array( 'items' => 'testfile.ext' ),
			'job.result' => array( 'items' => 'testfile2.ext' ),
			'job.status' => 1,
			'job.editor' => 'unittest',
			'job.mtime' => '2010-01-01 00:00:00',
			'job.ctime' => '2000-01-01 00:00:00',
		);

		$this->object = new \Aimeos\MAdmin\Job\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 2, $this->object->getSiteId() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest job', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'unittest job2' );
		$this->assertEquals( 'unittest job2', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetMethod()
	{
		$this->assertEquals( 'Product_Import_Text.importFile', $this->object->getMethod() );
	}


	public function testSetMethod()
	{
		$this->object->setMethod( 'Controller.method' );
		$this->assertEquals( 'Controller.method', $this->object->getMethod() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetParameter()
	{
		$this->assertEquals( array( 'items' => 'testfile.ext' ), $this->object->getParameter() );
	}


	public function testSetParameter()
	{
		$this->object->setParameter( array( 'items' => 'newfile.ext' ) );
		$this->assertEquals( array( 'items' => 'newfile.ext' ), $this->object->getParameter() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResult()
	{
		$this->assertEquals( array( 'items' => 'testfile2.ext' ), $this->object->getResult() );
	}


	public function testSetResult()
	{
		$this->object->setResult( array( 'items' => 'newfile.ext' ) );
		$this->assertEquals( array( 'items' => 'newfile.ext' ), $this->object->getResult() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->object->setStatus( -1 );
		$this->assertEquals( -1, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unittest', $this->object->getEditor() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2000-01-01 00:00:00', $this->object->getTimeCreated() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'job', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MAdmin\Job\Item\Standard();

		$list = $entries = array(
			'job.id' => 1,
			'job.label' => 'unittest job',
			'job.method' => 'Product_Import_Text.importFile',
			'job.parameter' => array( 'items' => 'testfile.ext' ),
			'job.result' => array( 'items' => 'testfile2.ext' ),
			'job.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['job.id'], $item->getId() );
		$this->assertEquals( $list['job.label'], $item->getLabel() );
		$this->assertEquals( $list['job.method'], $item->getMethod() );
		$this->assertEquals( $list['job.parameter'], $item->getParameter() );
		$this->assertEquals( $list['job.result'], $item->getResult() );
		$this->assertEquals( $list['job.status'], $item->getStatus() );
		$this->assertNull( $item->getSiteId() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( 1, $list['job.id'] );
		$this->assertEquals( 2, $list['job.siteid'] );
		$this->assertEquals( 'unittest job', $list['job.label'] );
		$this->assertEquals( 'Product_Import_Text.importFile', $list['job.method'] );
		$this->assertEquals( array( 'items' => 'testfile.ext' ), $list['job.parameter'] );
		$this->assertEquals( array( 'items' => 'testfile2.ext' ), $list['job.result'] );
		$this->assertEquals( 1, $list['job.status'] );
		$this->assertEquals( 'unittest', $list['job.editor'] );
		$this->assertEquals( '2010-01-01 00:00:00', $list['job.mtime'] );
		$this->assertEquals( '2000-01-01 00:00:00', $list['job.ctime'] );
	}
}
