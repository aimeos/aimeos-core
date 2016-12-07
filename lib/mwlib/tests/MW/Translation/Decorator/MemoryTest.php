<?php

namespace Aimeos\MW\Translation\Decorator;


/**
 * Test class for \Aimeos\MW\Translation\Decorator\Memory.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$strings = array( 'domain' => array(
			'test singular' => array( 0 => 'translation singular' ),
			'test plural' => array(
				0 => 'plural translation singular',
				1 => 'plural translation plural',
				2 => 'plural translation plural (cs)',
			)
		) );

		$conf = new \Aimeos\MW\Translation\None( 'cs' );
		$this->object = new \Aimeos\MW\Translation\Decorator\Memory( $conf, $strings );
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
		$this->assertEquals( 'translation singular', $this->object->dt( 'domain', 'test singular' ) );
	}

	public function testDtNone()
	{
		$this->assertEquals( 'test none', $this->object->dt( 'domain', 'test none' ) );
	}

	public function testDn()
	{
		$translation = $this->object->dn( 'domain', 'test plural', 'test plural 2', 1 );
		$this->assertEquals( 'plural translation singular', $translation );
	}

	public function testDnNone()
	{
		$translation = $this->object->dn( 'domain', 'test none', 'test none plural', 0 );
		$this->assertEquals( 'test none plural', $translation );
	}

	public function testDnPlural()
	{
		$translation = $this->object->dn( 'domain', 'test plural', 'test plural 2', 2 );
		$this->assertEquals( 'plural translation plural', $translation );
	}

	public function testDnPluralCs()
	{
		$translation = $this->object->dn( 'domain', 'test plural', 'test plural 2', 5 );
		$this->assertEquals( 'plural translation plural (cs)', $translation );
	}
}
