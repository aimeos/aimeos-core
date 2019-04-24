<?php


namespace Aimeos\Admin\JQAdm;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperJqadm::getContext();
		$paths = \TestHelperJqadm::getTemplatePaths();

		// $this->object = new \Aimeos\Admin\JQAdm\...\Standard( $this->context, $paths );
	}


	protected function tearDown()
	{
		\Aimeos\MShop::cache( false );

		unset( $this->object );
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}
