<?php


namespace Aimeos\Client\JsonApi;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperJapi::getContext();
		$paths = \TestHelperJapi::getTemplatePaths();

		// $this->object = new \Aimeos\Client\JsonApi\..._Standard( $this->context, $paths );
		// $this->object->setView( \TestHelperJapi::getView() );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );

		unset( $this->object );
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}
