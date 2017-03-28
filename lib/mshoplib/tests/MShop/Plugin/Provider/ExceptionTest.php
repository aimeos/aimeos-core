<?php

namespace Aimeos\MShop\Plugin\Provider;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
	private $codes;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->codes = array( 'something' => array( 'went', 'terribly', 'wrong' ) );

	}

	public function test()
	{
		try {
			throw new \Aimeos\MShop\Plugin\Provider\Exception( 'msg', 13, null, $this->codes );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( 13, $mppe->getCode() );
			$this->assertEquals( 'msg', $mppe->getMessage() );
			$this->assertEquals( $this->codes, $mppe->getErrorCodes() );

		}

		try {
			throw new \Aimeos\MShop\Plugin\Provider\Exception( 'msg2', 11 );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( [], $e->getErrorCodes() );
		}
	}
}