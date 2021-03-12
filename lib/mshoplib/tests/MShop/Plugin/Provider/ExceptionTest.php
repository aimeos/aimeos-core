<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider;


class ExceptionTest extends \PHPUnit\Framework\TestCase
{
	private $codes;


	protected function setUp() : void
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
