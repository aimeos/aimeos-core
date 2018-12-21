<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Controller\Jobs;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	public function testGetValue()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$object = new TestBase( $context, $aimeos );

		$this->assertEquals( 'value', $object->getValuePublic( ['key' => ' value '], 'key', 'def' ) );
		$this->assertEquals( 'def', $object->getValuePublic( ['key' => ' '], 'key', 'def' ) );
		$this->assertEquals( 'def', $object->getValuePublic( [], 'key', 'def' ) );
	}
}



class TestBase extends \Aimeos\Controller\Jobs\Base
{
	public function getValuePublic( $list, $key, $default )
	{
		return $this->getValue( $list, $key, $default );
	}
}