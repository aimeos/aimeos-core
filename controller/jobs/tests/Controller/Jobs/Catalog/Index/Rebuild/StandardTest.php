<?php

namespace Aimeos\Controller\Jobs\Catalog\Index\Rebuild;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Catalog\Index\Rebuild\Standard( $context, $aimeos );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Catalog index rebuild', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Rebuilds the catalog index for searching products';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();


		$name = 'ControllerJobsCatalogIndexRebuildDefaultRun';
		$context->getConfig()->set( 'classes/index/manager/name', $name );


		$indexManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Index\\Manager\\Standard' )
			->setMethods( array( 'rebuildIndex', 'cleanupIndex' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Catalog\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Index\\Manager\\' . $name, $indexManagerStub );


		$indexManagerStub->expects( $this->once() )->method( 'rebuildIndex' );
		$indexManagerStub->expects( $this->once() )->method( 'cleanupIndex' );


		$object = new \Aimeos\Controller\Jobs\Catalog\Index\Rebuild\Standard( $context, $aimeos );
		$object->run();
	}
}
