<?php

namespace Aimeos\Perf\Config;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class ArrayTest extends \PHPUnit_Framework_TestCase
{
	public function testArray()
	{
		$start = microtime( true );

		$paths = array(
			__DIR__ . DIRECTORY_SEPARATOR . 'one',
			__DIR__ . DIRECTORY_SEPARATOR . 'two',
		);

		for( $i = 0; $i < 1000; $i++ )
		{
			$conf = new \Aimeos\MW\Config\PHPArray( $paths );

			$conf->get( 'test/db/host' );
			$conf->get( 'test/db/username' );
			$conf->get( 'test/db/password' );
		}

		$stop = microtime( true );
		echo "\n    config array: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
