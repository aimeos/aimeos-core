<?php

namespace Aimeos\MW\MQueue\Message;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$row = array( 'id' => 1, 'message' => 'test', 'cname', 'unittest', 'rtime' => '2000-01-01 00:00:00' );
		$this->object = new \Aimeos\MW\MQueue\Message\Standard( $row );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testId()
	{
		$this->assertEquals( '1', $this->object->getId() );
	}


	public function testBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}
}
