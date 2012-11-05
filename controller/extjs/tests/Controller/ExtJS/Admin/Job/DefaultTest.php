<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Admin_Job_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Admin_Job_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Admin_Job_Default( TestHelper::getContext() );
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


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'job.method' => 'Controller.method' ) ) ) ),
			'sort' => 'job.ctime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 'unittest job', $result['items'][0]->{'job.label'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'job.label' => 'test job',
				'job.method' => 'test.job',
				'job.parameter' => array( 'test' => 'job' ),
				'job.result' => array( 'items' => 'testfile2.ext' ),
				'job.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'job.method' => 'test.job' ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'job.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'job.id'} );
		$this->assertEquals( $saved['items']->{'job.id'}, $searched['items'][0]->{'job.id'});
		$this->assertEquals( $saved['items']->{'job.label'}, $searched['items'][0]->{'job.label'});
		$this->assertEquals( $saved['items']->{'job.method'}, $searched['items'][0]->{'job.method'});
		$this->assertEquals( $saved['items']->{'job.parameter'}, $searched['items'][0]->{'job.parameter'});
		$this->assertEquals( $saved['items']->{'job.result'}, $searched['items'][0]->{'job.result'});
		$this->assertEquals( $saved['items']->{'job.status'}, $searched['items'][0]->{'job.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}

	public function testSaveItemException()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => 'bad data',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$saved = $this->_object->saveItems( $saveParams );
	}

}
