<?php

namespace Aimeos\Client\JQAdm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class DemoTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->context = \TestHelperJqadm::getContext();
		$paths = \TestHelperJqadm::getHtmlTemplatePaths();

		// $this->object = new \Aimeos\Client\JQAdm\...\Standard( $this->context, $paths );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );

		\Aimeos\MShop\Factory::clear();
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}