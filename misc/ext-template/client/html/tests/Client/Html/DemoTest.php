<?php

namespace Aimeos\Client\Html;


class DemoTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();

		// $this->object = new \Aimeos\Client\Html\..._Standard( $this->context, $paths );
		// $this->object->setView( \TestHelperHtml::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
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
