<?php

namespace Aimeos\Controller\Jobs;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function testGetTypeItemNotFound()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$object = new TestAbstract( $context, $aimeos );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$object->getTypeItemPublic( 'product/type', 'product', 'test' );
	}
}



class TestAbstract extends \Aimeos\Controller\Jobs\Base
{
	public function getTypeItemPublic( $prefix, $domain, $code )
	{
		$this->getTypeItem( $prefix, $domain, $code );
	}
}