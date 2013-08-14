<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Admin_Job_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_jobItemStub;
	private $_jobManagerStub;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_Jobs_Admin_Job_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$this->_jobItemStub = $this->getMockBuilder( 'MAdmin_Job_Item_Default' )->getMock();

		$this->_jobManagerStub = $this->getMockBuilder( 'MAdmin_Job_Manager_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$this->_object = new Controller_Jobs_Admin_Job_Default( $context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Admin interface jobs', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Executes the jobs created by the admin interface, e.g. the text exports';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();

		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'classes/job/manager/name', $name );
		$context->getConfig()->set( 'classes/controller/extjs/admin/job/name', $name );

		MAdmin_Job_Manager_Factory::injectManager( 'MAdmin_Job_Manager_' . $name, $this->_jobManagerStub );

		$adminJobCntlStub = $this->getMockBuilder( 'Controller_ExtJS_Admin_Job_Default' )
			->setMethods( array( 'deleteItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_' . $name, $adminJobCntlStub );


		$adminJobCntlStub->expects( $this->once() )->method( 'deleteItem' )
			->will( $this->returnValue( array( 'number' => 42 ) ) );

		$this->_jobManagerStub->expects( $this->atLeastOnce() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $this->_jobItemStub ), array() ) );

		$this->_jobManagerStub->expects( $this->once() )->method( 'saveItem' );

		$this->_jobItemStub->expects( $this->atLeastOnce() )->method( 'getMethod' )
			->will( $this->returnValue( 'Admin_Job.deleteItem' ) );

		$this->_jobItemStub->expects( $this->once() )->method( 'setResult' )
			->with( $this->equalTo( array( 'number' => 42 ) ) );

		$this->_jobItemStub->expects( $this->once() )->method( 'setStatus' )
			->with( $this->equalTo( 0 ) );


		$object = new Controller_Jobs_Admin_Job_Default( $context );
		$object->run();
	}


	/**
	 * @dataProvider methodProvider
	 */
	public function testRunInvalidMethod( $method )
	{
		$context = TestHelper::getContext();
		$name = 'ControllerJobsAdminJobDefaultRun';
		$context->getConfig()->set( 'classes/job/manager/name', $name );

		$object = new Controller_Jobs_Admin_Job_Default( $context );
		MAdmin_Job_Manager_Factory::injectManager( 'MAdmin_Job_Manager_' . $name, $this->_jobManagerStub );


		$this->_jobManagerStub->expects( $this->atLeastOnce() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $this->_jobItemStub ), array() ) );

		$this->_jobManagerStub->expects( $this->once() )->method( 'saveItem' );

		$this->_jobItemStub->expects( $this->atLeastOnce() )->method( 'getMethod' )
			->will( $this->returnValue( $method ) );

		$this->_jobItemStub->expects( $this->once() )->method( 'setStatus' )
			->with( $this->equalTo( -1 ) );


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
