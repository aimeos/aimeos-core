<?php

namespace Aimeos\Client\Html\Common\Decorator;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
	}


	public function testDecorateFactoryClientCommon()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array() );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateFactoryClientGlobal()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/catalog/filter/decorators/global', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array() );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateSubClientCommon()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array() )->getSubClient( 'tree' );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateSubClientGlobal()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/catalog/filter/tree/decorators/global', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context, array() )->getSubClient( 'tree' );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}
}