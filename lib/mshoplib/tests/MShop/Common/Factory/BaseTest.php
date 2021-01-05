<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Common\Factory;


/**
 * Test class for \Aimeos\MShop\Common\Factory\Base.
 */
class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$config = $this->context->getConfig();

		$config->set( 'mshop/common/manager/decorators/default', [] );
		$config->set( 'mshop/attribute/manager/decorators/global', [] );
		$config->set( 'mshop/attribute/manager/decorators/local', [] );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop\Attribute\Manager\Factory::injectManager( '\Aimeos\MShop\Attribute\Manager\StandardMock', null );
	}


	public function testInjectManager()
	{
		$manager = \Aimeos\MShop\Attribute\Manager\Factory::create( $this->context, 'Standard' );
		\Aimeos\MShop\Attribute\Manager\Factory::injectManager( '\Aimeos\MShop\Attribute\Manager\StandardMock', $manager );

		$injectedManager = \Aimeos\MShop\Attribute\Manager\Factory::create( $this->context, 'StandardMock' );

		$this->assertSame( $manager, $injectedManager );
	}


	public function testInjectManagerReset()
	{
		$manager = \Aimeos\MShop\Attribute\Manager\Factory::create( $this->context, 'Standard' );
		\Aimeos\MShop\Attribute\Manager\Factory::injectManager( '\Aimeos\MShop\Attribute\Manager\StandardMock', $manager );
		\Aimeos\MShop\Attribute\Manager\Factory::injectManager( '\Aimeos\MShop\Attribute\Manager\StandardMock', null );

		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop\Attribute\Manager\Factory::create( $this->context, 'StandardMock' );
	}

}
