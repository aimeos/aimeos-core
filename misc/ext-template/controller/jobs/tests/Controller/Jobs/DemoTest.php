<?php

namespace Aimeos\Controller\Jobs;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$aimeos = \TestHelperJobs::getAimeos();
		$context = \TestHelperJobs::getContext();

		// $this->object = new \Aimeos\Controller\Jobs\Demo\Standard( $context, $aimeos );
	}


	protected function tearDown()
	{
		unset( $this->object );
		\Aimeos\MShop\Factory::clear();
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}