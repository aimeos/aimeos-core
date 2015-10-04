<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Admin_Job_StandardTest extends PHPUnit_Framework_TestCase
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
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->jobItemStub = $this->getMockBuilder( 'MAdmin_Job_Item_Standard' )->getMock();

		$this->jobManagerStub = $this->getMockBuilder( 'MAdmin_Job_Manager_Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$this->object = new Controller_Jobs_Admin_Job_Standard( $context, $aimeos );
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
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'classes/job/manager/name', $name );
		$context->getConfig()->set( 'classes/controller/extjs/admin/job/name', $name );

		MAdmin_Job_Manager_Factory::injectManager( 'MAdmin_Job_Manager_' . $name, $this->jobManagerStub );

		$adminJobCntlStub = $this->getMockBuilder( 'Controller_ExtJS_Admin_Job_Standard' )
			->setMethods( array( 'deleteItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_' . $name, $adminJobCntlStub );


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


		$object = new Controller_Jobs_Admin_Job_Standard( $context, $aimeos );
		$object->run();
	}


	/**
	 * @dataProvider methodProvider
	 */
	public function testRunInvalidMethod( $method )
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'classes/job/manager/name', $name );

		$object = new Controller_Jobs_Admin_Job_Standard( $context, $aimeos );
		MAdmin_Job_Manager_Factory::injectManager( 'MAdmin_Job_Manager_' . $name, $this->jobManagerStub );


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
