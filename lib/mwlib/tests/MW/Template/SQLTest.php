<?php

namespace Aimeos\MW\Template;


/**
 * Test class for \Aimeos\MW\Session\CMSLite.
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class SQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$template = 'SELECT * FROM /*-FROM*/table/*FROM-*/';

		$this->object = new \Aimeos\MW\Template\SQL( $template );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown() : void
	{
	}

	public function testToString()
	{
		$template = $this->object->get( 'FROM' );
		$this->assertInstanceOf( \Aimeos\MW\Template\Iface::class, $template );

		$this->assertEquals( 'table', $template->str() );
	}
}
