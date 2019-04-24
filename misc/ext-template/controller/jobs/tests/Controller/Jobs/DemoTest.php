<?php

namespace Aimeos\Controller\Jobs;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop::cache( true );

		$aimeos = \TestHelperJobs::getAimeos();
		$context = \TestHelperJobs::getContext();

		// $this->object = new \Aimeos\Controller\Jobs\Demo\Standard( $context, $aimeos );
	}


	protected function tearDown()
	{
		\Aimeos\MShop::cache( false );

		unset( $this->object );
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}
