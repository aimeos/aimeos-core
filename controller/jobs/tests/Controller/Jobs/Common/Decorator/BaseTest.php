<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Controller\Jobs\Common\Decorator;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $stub;
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->stub = $this->getMockBuilder( \Aimeos\Controller\Jobs\Iface::class )->getMock();
		$this->object = new TestBase( $this->stub, $context, $aimeos );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetContext()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $this->object->getContextPublic() );
	}


	public function testGetAimeos()
	{
		$this->assertInstanceOf( \Aimeos\Bootstrap::class, $this->object->getAimeosPublic() );
	}


	public function testCall()
	{
		$this->markTestInComplete( 'PHP warning is triggered instead of exception' );
	}

}


class TestBase
	extends \Aimeos\Controller\Jobs\Common\Decorator\Base
{
	public function getContextPublic()
	{
		return $this->getContext();
	}

	public function getAimeosPublic()
	{
		return $this->getAimeos();
	}
}
