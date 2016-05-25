<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

namespace Aimeos\MW\DB\Connection;


class NoneTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MW\DB\Connection\None();
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreate()
	{
		$this->setExpectedException( '\Aimeos\MW\DB\Exception' );
		$this->object->create( 'SELECT' );
	}


	public function testGetRawObject()
	{
		$this->setExpectedException( '\Aimeos\MW\DB\Exception' );
		$this->object->getRawObject();
	}


	public function testBegin()
	{
		$this->setExpectedException( '\Aimeos\MW\DB\Exception' );
		$this->object->begin();
	}


	public function testCommit()
	{
		$this->setExpectedException( '\Aimeos\MW\DB\Exception' );
		$this->object->commit();
	}


	public function testRollback()
	{
		$this->setExpectedException( '\Aimeos\MW\DB\Exception' );
		$this->object->rollback();
	}
}
