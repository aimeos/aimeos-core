<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\Jobs\Common\Decorator;


/**
 * Test class for \Aimeos\Controller\Jobs\Common\Decorator\BaseTest.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
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
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Admin\\Job\\Standard' )
			->setConstructorArgs( array( $context, $aimeos ) )
			->getMock();

		$this->object = new TestBase( $this->stub, $context, $aimeos );
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->object->getContextPublic() );
	}


	public function testGetAimeos()
	{
		$this->assertInstanceOf( '\Aimeos\Bootstrap', $this->object->getAimeosPublic() );
	}


	public function testCall()
	{
		$this->markTestInComplete( 'PHP warning is triggered instead of exception' );
	}

}


class TestBase
	extends \Aimeos\Controller\Jobs\Common\Decorator\Base
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
