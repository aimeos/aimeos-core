<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	public function setUp()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_TestAbstract( $context, $arcavias );
	}


	public function testGetTypeItemNotFound()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getTypeItemPublic( 'product/type', 'product', 'test' );
	}


	public function testGetTemplateNotFound()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getTemplatePublic( 'test', 'test' );
	}
}



class Controller_Jobs_TestAbstract extends Controller_Jobs_Abstract
{
	public function getTemplatePublic( $confpath, $default )
	{
		$this->_getTemplate( $confpath, $default );
	}

	public function getTypeItemPublic( $prefix, $domain, $code )
	{
		$this->_getTypeItem( $prefix, $domain, $code );
	}
}