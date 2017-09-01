<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\Admin\JQAdm;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperJqadm::getContext();
		$paths = \TestHelperJqadm::getTemplatePaths();

		// $this->object = new \Aimeos\Admin\JQAdm\...\Standard( $this->context, $paths );
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