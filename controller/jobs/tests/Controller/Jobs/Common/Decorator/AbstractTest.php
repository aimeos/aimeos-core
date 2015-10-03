<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_Jobs_Common_Decorator_BaseTest.
 */
class Controller_Jobs_Common_Decorator_BaseTest extends PHPUnit_Framework_TestCase
{
	private $stub;
	private $object;


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

		$this->stub = $this->getMockBuilder( 'Controller_Jobs_Admin_Job_Default' )
			->setConstructorArgs( array( $context, $aimeos ) )
			->getMock();

		$this->object = new Controller_Jobs_Common_Decorator_BaseImpl( $context, $aimeos, $this->stub );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetName()
	{
		$this->stub->expects( $this->once() )
			->method( 'getName' )
			->will( $this->returnValue( 'test name' ) );

		$this->assertEquals( 'test name', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$this->stub->expects( $this->once() )
			->method( 'getDescription' )
			->will( $this->returnValue( 'test description' ) );

		$this->assertEquals( 'test description', $this->object->getDescription() );
	}


	public function testRun()
	{
		$this->object->run();
	}


	public function testGetContext()
	{
		$this->assertInstanceOf( 'MShop_Context_Item_Interface', $this->object->getContextPublic() );
	}


	public function testGetAimeos()
	{
		$this->assertInstanceOf( 'Aimeos', $this->object->getAimeosPublic() );
	}


	public function testCall()
	{
		$this->markTestInComplete( 'PHP warning is triggered instead of exception' );
	}

}


class Controller_Jobs_Common_Decorator_BaseImpl
	extends Controller_Jobs_Common_Decorator_Base
{
	public function getContextPublic()
	{
		return $this->getContext();
	}

	public function getAimeosPublic()
	{
		return $this->getAimeos();
	}
}
