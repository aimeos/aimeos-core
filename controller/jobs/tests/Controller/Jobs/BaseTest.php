<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Controller\Jobs;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$context->getConfig()->set( 'controller/jobs/to-email', 'me@localhost' );

		$this->object = $this->getMockForAbstractClass( '\Aimeos\Controller\Jobs\Base', [$context, $aimeos] );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetValue()
	{
		$method = $this->access( 'getValue' );

		$this->assertEquals( 'value', $method->invokeArgs( $this->object, [['key' => ' value '], 'key', 'def'] ) );
		$this->assertEquals( 'def', $method->invokeArgs( $this->object, [['key' => ' '], 'key', 'def'] ) );
		$this->assertEquals( 'def', $method->invokeArgs( $this->object, [[], 'key', 'def'] ) );
	}


	public function testMail()
	{
		$result = $this->access( 'mail' )->invokeArgs( $this->object, ['me@localhost', 'test'] );
		$this->assertSame( $this->object, $result );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Controller\Jobs\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
