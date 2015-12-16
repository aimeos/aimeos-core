<?php

namespace Aimeos\Controller\Frontend\Plugin\Decorator;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class ExampleTest extends \PHPUnit_Framework_TestCase
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
		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );
		$this->object = new \Aimeos\Controller\Frontend\Service\Decorator\Example( $controller, $context );
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


	public function testCall()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Service\\Exception' );
		$this->object->checkServiceAttributes( 'delivery', -1, array() );
	}

}
