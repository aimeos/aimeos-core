<?php

namespace Aimeos\Client\Html;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $templatePaths;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$this->templatePaths = \TestHelper::getHtmlTemplatePaths();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateClient()
	{
		$client = \Aimeos\Client\Html\Factory::createClient( $this->context, $this->templatePaths, 'account/favorite' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientName()
	{
		$client = \Aimeos\Client\Html\Factory::createClient( $this->context, $this->templatePaths, 'account/favorite', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientNameEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Factory::createClient( $this->context, $this->templatePaths, '' );
	}


	public function testCreateClientNameParts()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Factory::createClient( $this->context, $this->templatePaths, 'account_favorite' );
	}


	public function testCreateClientNameInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Factory::createClient( $this->context, $this->templatePaths, '%account/favorite' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Favorite\Factory::createClient( $this->context, $this->templatePaths, 'account/fav' );
	}

}
