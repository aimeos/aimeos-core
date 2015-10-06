<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $object;


	public function setUp()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->object = new Controller_Jobs_TestAbstract( $context, $aimeos );
	}


	public function testGetTypeItemNotFound()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getTypeItemPublic( 'product/type', 'product', 'test' );
	}


	public function testGetTemplateNotFound()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getTemplatePublic( 'test', 'test' );
	}
}



class Controller_Jobs_TestAbstract extends Controller_Jobs_Abstract
{
	public function getTemplatePublic( $confpath, $default )
	{
		$this->getTemplate( $confpath, $default );
	}

	public function getTypeItemPublic( $prefix, $domain, $code )
	{
		$this->getTypeItem( $prefix, $domain, $code );
	}
}