<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\Client\Html\Common\Factory;


/**
 * Test class for \Aimeos\Client\Html\Common\Factory\BaseTest.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'client/html/common/decorators/default', array() );
		$config->set( 'client/html/admin/decorators/global', array() );
		$config->set( 'client/html/admin/decorators/local', array() );
	}


	public function testInjectClient()
	{
		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array(), 'Standard' );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', $client );

		$injectedClient = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array(), 'Standard' );

		$this->assertSame( $client, $injectedClient );
	}


	public function testInjectClientReset()
	{
		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array(), 'Standard' );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', $client );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', null );

		$new = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array(), 'Standard' );

		$this->assertNotSame( $client, $new );
	}

}