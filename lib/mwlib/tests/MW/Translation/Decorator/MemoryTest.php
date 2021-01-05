<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Translation\Decorator;


class MemoryTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
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


	protected function tearDown() : void
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
