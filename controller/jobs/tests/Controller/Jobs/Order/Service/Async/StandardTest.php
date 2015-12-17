<?php

namespace Aimeos\Controller\Jobs\Order\Service\Async;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Order\Service\Async\Standard( $context, $aimeos );
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
		$this->assertEquals( 'Batch update of payment/delivery status', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Executes payment or delivery service providers that uses batch updates';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();


		$name = 'ControllerJobsServiceAsyncProcessDefaultRun';
		$context->getConfig()->set( 'mshop/service/manager/name', $name );


		$serviceManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Manager\\Standard' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Service\\Manager\\' . $name, $serviceManagerStub );


		$serviceItem = $serviceManagerStub->createItem();

		$serviceProviderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Delivery\\Manual' )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();


		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->returnValue( $serviceProviderStub ) );

		$serviceProviderStub->expects( $this->once() )->method( 'updateAsync' );


		$object = new \Aimeos\Controller\Jobs\Order\Service\Async\Standard( $context, $aimeos );
		$object->run();
	}


	public function testRunExceptionProcess()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();


		$name = 'ControllerJobsServiceAsyncProcessDefaultRun';
		$context->getConfig()->set( 'mshop/service/manager/name', $name );


		$serviceManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Manager\\Standard' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Service\\Manager\\' . $name, $serviceManagerStub );


		$serviceItem = $serviceManagerStub->createItem();

		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->throwException( new \Aimeos\MShop\Service\Exception() ) );


		$object = new \Aimeos\Controller\Jobs\Order\Service\Async\Standard( $context, $aimeos );
		$object->run();
	}
}
