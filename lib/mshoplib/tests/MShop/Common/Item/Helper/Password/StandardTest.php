<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Test class for \Aimeos\MShop\Common\Item\Helper\Password\Standard
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Common\Item\Helper\Password\Standard( array( 'format' => '{%2$s}%1$s' ) );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}

	
	public function testEncode()
	{
		$this->assertEquals( '14f40cc1311a52a6021b93a155ed719aac2bdb70', $this->object->encode( 'unittest', 'salt' ) );
	}
}
