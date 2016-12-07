<?php

namespace Aimeos\MW\Template;


/**
 * Test class for \Aimeos\MW\Session\CMSLite.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class T3Test extends \PHPUnit_Framework_TestCase
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
		$template = 'TYPO3 Template <!--###NAME-->Name<!--NAME###-->';

		$this->object = new \Aimeos\MW\Template\T3( $template );
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


	public function testToString()
	{
		$template = $this->object->get('NAME');
		$this->assertInstanceOf( '\\Aimeos\\MW\\Template\\Iface', $template );

		$this->assertEquals( 'Name', $template->str() );
	}
}
