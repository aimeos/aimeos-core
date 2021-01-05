<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

namespace Aimeos\MW\DB\Connection;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\DB\Connection\None();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConnect()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->connect();
	}


	public function testCreate()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->create( 'SELECT' );
	}


	public function testGetRawObject()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->getRawObject();
	}


	public function testBegin()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->begin();
	}


	public function testCommit()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->commit();
	}


	public function testRollback()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->rollback();
	}
}
