<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\MW\Media\Application;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'application.txt' );

		$media = new \Aimeos\MW\Media\Application\Standard( $content, 'application/octet-stream', [] );

		$this->assertEquals( 'application/octet-stream', $media->getMimetype() );
	}


	public function testSave()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'application.txt' );

		$media = new \Aimeos\MW\Media\Application\Standard( $content, 'application/octet-stream', [] );

		$this->assertEquals( 'some text', $media->save() );
	}


	public function testSaveFile()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'application.txt' );
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'application.txt';

		$media = new \Aimeos\MW\Media\Application\Standard( $content, 'application/octet-stream', [] );
		$media->save( $dest );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}
}
