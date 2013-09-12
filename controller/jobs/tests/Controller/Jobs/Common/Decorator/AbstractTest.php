<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Jobs_Common_Decorator_AbstractTest.
 */
class Controller_Jobs_Common_Decorator_AbstractTest extends MW_Unittest_Testcase
{
	private $_stub;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_stub = $this->getMockBuilder( 'Controller_Jobs_Admin_Job_Default' )
			->setConstructorArgs( array( $context, $arcavias ) )
			->getMock();

		$params = array( $context, $arcavias, $this->_stub );
		$this->_object = $this->getMockForAbstractClass( 'Controller_Jobs_Common_Decorator_Abstract', $params );
	}


	public function testGetName()
	{
        $this->_stub->expects( $this->once() )
             ->method( 'getName' )
             ->will( $this->returnValue( 'test name' ) );

        $this->assertEquals( 'test name', $this->_object->getName() );
	}


	public function testGetDescription()
	{
        $this->_stub->expects( $this->once() )
             ->method( 'getDescription' )
             ->will( $this->returnValue( 'test description' ) );

        $this->assertEquals( 'test description', $this->_object->getDescription() );
	}


	public function testRun()
	{
        $this->_stub->expects( $this->once() )
             ->method( 'run' )
             ->will( $this->returnValue( true ) );

        $this->assertTrue( $this->_object->run() );
	}

}