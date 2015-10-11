<?php

namespace Aimeos\Controller\Jobs\Admin\Job;


/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $jobItemStub;
	private $jobManagerStub;


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

		$this->jobItemStub = $this->getMockBuilder( '\\Aimeos\\MAdmin\\Job\\Item\\Standard' )->getMock();

		$this->jobManagerStub = $this->getMockBuilder( '\\Aimeos\\MAdmin\\Job\\Manager\\Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$this->object = new \Aimeos\Controller\Jobs\Admin\Job\Standard( $context, $aimeos );
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
		$this->assertEquals( 'Admin interface jobs', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Executes the jobs created by the admin interface, e.g. the text exports';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'madmin/job/manager/name', $name );
		$context->getConfig()->set( 'controller/extjs/admin/job/name', $name );

		\Aimeos\MAdmin\Job\Manager\Factory::injectManager( '\\Aimeos\\MAdmin\\Job\\Manager\\' . $name, $this->jobManagerStub );

		$adminJobCntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\Standard' )
			->setMethods( array( 'deleteItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\Controller\ExtJS\Admin\Job\Factory::injectController( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\' . $name, $adminJobCntlStub );


		$adminJobCntlStub->expects( $this->once() )->method( 'deleteItem' )
			->will( $this->returnValue( array( 'number' => 42 ) ) );

		$this->jobManagerStub->expects( $this->atLeastOnce() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $this->jobItemStub ), array() ) );

		$this->jobManagerStub->expects( $this->once() )->method( 'saveItem' );

		$this->jobItemStub->expects( $this->atLeastOnce() )->method( 'getMethod' )
			->will( $this->returnValue( 'Admin_Job.deleteItem' ) );

		$this->jobItemStub->expects( $this->once() )->method( 'setResult' )
			->with( $this->equalTo( array( 'number' => 42 ) ) );

		$this->jobItemStub->expects( $this->once() )->method( 'setStatus' )
			->with( $this->equalTo( -1 ) );


		$object = new \Aimeos\Controller\Jobs\Admin\Job\Standard( $context, $aimeos );
		$object->run();
	}


	/**
	 * @dataProvider methodProvider
	 */
	public function testRunInvalidMethod( $method )
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'madmin/job/manager/name', $name );

		$object = new \Aimeos\Controller\Jobs\Admin\Job\Standard( $context, $aimeos );
		\Aimeos\MAdmin\Job\Manager\Factory::injectManager( '\\Aimeos\\MAdmin\\Job\\Manager\\' . $name, $this->jobManagerStub );


		$this->jobManagerStub->expects( $this->atLeastOnce() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $this->jobItemStub ), array() ) );

		$this->jobManagerStub->expects( $this->once() )->method( 'saveItem' );

		$this->jobItemStub->expects( $this->atLeastOnce() )->method( 'getMethod' )
			->will( $this->returnValue( $method ) );

		$this->jobItemStub->expects( $this->once() )->method( 'setStatus' )
			->with( $this->equalTo( 0 ) );


		$object->run();
	}


	public function methodProvider()
	{
		return array(
			array( 'ex-ample.test' ),
			array( 'example.test-1' ),
			array( 'example.test_01' ),
			array( 'example_test.test' ),
			array( 'Admin_Job.test' ),
		);
	}
}
