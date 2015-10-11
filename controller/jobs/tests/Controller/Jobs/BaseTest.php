<?php

namespace Aimeos\Controller\Jobs;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	public function setUp()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->object = new TestAbstract( $context, $aimeos );
	}


	public function testGetTypeItemNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getTypeItemPublic( 'product/type', 'product', 'test' );
	}


	public function testGetTemplateNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getTemplatePublic( 'test', 'test' );
	}
}



class TestAbstract extends \Aimeos\Controller\Jobs\Base
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