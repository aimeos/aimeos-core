<?php

namespace Aimeos\Admin\Jsonadm;


class DemoTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		// $this->object = new \Aimeos\Admin\Jsonadm\Demo\Standard( \TestHelperJsonadm::getContext() );
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