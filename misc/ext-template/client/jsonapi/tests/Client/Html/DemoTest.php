<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Client\Jsonapi;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperJapi::getContext();
		$paths = \TestHelperJapi::getTemplatePaths();

		// $this->object = new \Aimeos\Client\Jsonapi\..._Standard( $this->context, $paths );
		// $this->object->setView( \TestHelperJapi::getView() );
	}


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