<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Client_Html_Common_Decorator_ClientTest extends PHPUnit_Framework_TestCase
{
	private $_context;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
	}


	public function testDecorateFactoryClientCommon()
	{
		$config = $this->_context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array() );

		$this->assertInstanceOf( 'Client_Html_Common_Decorator_Interface', $object );
	}


	public function testDecorateFactoryClientGlobal()
	{
		$config = $this->_context->getConfig();
		$config->set( 'client/html/catalog/filter/decorators/global', array( 'Example' ) );

		$object = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array() );

		$this->assertInstanceOf( 'Client_Html_Common_Decorator_Interface', $object );
	}


	public function testDecorateSubClientCommon()
	{
		$config = $this->_context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array() )->getSubClient( 'tree' );

		$this->assertInstanceOf( 'Client_Html_Common_Decorator_Interface', $object );
	}


	public function testDecorateSubClientGlobal()
	{
		$config = $this->_context->getConfig();
		$config->set( 'client/html/catalog/filter/tree/decorators/global', array( 'Example' ) );

		$object = Client_Html_Catalog_Filter_Factory::createClient( $this->_context, array() )->getSubClient( 'tree' );

		$this->assertInstanceOf( 'Client_Html_Common_Decorator_Interface', $object );
	}
}