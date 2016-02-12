<?php

namespace Aimeos\Client\Html\Account\Download;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $templatePaths;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->templatePaths = \TestHelperHtml::getHtmlTemplatePaths();
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateClient()
	{
		$client = \Aimeos\Client\Html\Account\Download\Factory::createClient( $this->context, $this->templatePaths );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientName()
	{
		$client = \Aimeos\Client\Html\Account\Download\Factory::createClient( $this->context, $this->templatePaths, 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientNameInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Download\Factory::createClient( $this->context, $this->templatePaths, '$$$' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Download\Factory::createClient( $this->context, $this->templatePaths, 'notfound' );
	}

}
