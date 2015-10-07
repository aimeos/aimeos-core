<?php

namespace Aimeos\MW\Translation\Decorator;


/**
 * Test class for \Aimeos\MW\Translation\Decorator\APC.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class APCTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( function_exists( 'apc_store' ) === false ) {
			$this->markTestSkipped( 'APC not installed' );
		}

		$trans = new \Aimeos\MW\Translation\None( 'en_GB' );
		$this->object = new \Aimeos\MW\Translation\Decorator\APC( $trans, 'i18n' );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
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
