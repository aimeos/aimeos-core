<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Translation\Decorator;


class APCTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$trans = new \Aimeos\MW\Translation\None( 'en_GB' );
		$this->object = new \Aimeos\MW\Translation\Decorator\APC( $trans, 'i18n' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testDt()
	{
		$this->assertEquals( 'test', $this->object->dt( 'domain', 'test' ) );
	}


	public function testDn()
	{
		$this->assertEquals( 'tests', $this->object->dn( 'domain', 'test', 'tests', 2 ) );
	}


	public function testGetLocale()
	{
		$this->assertEquals( 'en_GB', $this->object->getLocale() );
	}
}
