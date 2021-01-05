<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Controller;


class JobsTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateEmpty()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->expectException( \Aimeos\Controller\Jobs\Exception::class );
		\Aimeos\Controller\Jobs::create( $context, $aimeos, "\t\n" );
	}


	public function testCreateInvalidName()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->expectException( \Aimeos\Controller\Jobs\Exception::class );
		\Aimeos\Controller\Jobs::create( $context, $aimeos, '%^' );
	}


	public function testCreateNotExisting()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->expectException( \Aimeos\Controller\Jobs\Exception::class );
		\Aimeos\Controller\Jobs::create( $context, $aimeos, 'notexist' );
	}


	public function testGet()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$list = \Aimeos\Controller\Jobs::get( $context, $aimeos, \TestHelperJobs::getControllerPaths() );

		$this->assertEquals( 0, count( $list ) );
	}
}
