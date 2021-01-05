<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MShop\Common\Helper\Config;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	public function testCheckBoolean()
	{
		$def = ['code' => 'key', 'type' => 'boolean', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => '0'] ) );
	}


	public function testCheckBooleanInvalid()
	{
		$def = ['code' => 'key', 'type' => 'boolean', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a true/false value'], $object->check( ['key' => 'a'] ) );
	}


	public function testCheckString()
	{
		$def = ['code' => 'key', 'type' => 'string', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => 'abc'] ) );
	}


	public function testCheckStringInvalid()
	{
		$def = ['code' => 'key', 'type' => 'string', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a string'], $object->check( ['key' => new \stdClass()] ) );
	}


	public function testCheckText()
	{
		$def = ['code' => 'key', 'type' => 'text', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => 'abc'] ) );
	}


	public function testCheckTextInvalid()
	{
		$def = ['code' => 'key', 'type' => 'text', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a string'], $object->check( ['key' => new \stdClass()] ) );
	}


	public function testCheckInteger()
	{
		$def = ['code' => 'key', 'type' => 'integer', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => '123'] ) );
		$this->assertEquals( ['key' => null], $object->check( ['key' => 123] ) );
	}


	public function testCheckIntegerInvalid()
	{
		$def = ['code' => 'key', 'type' => 'integer', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not an integer number'], $object->check( ['key' => 'abc'] ) );
	}


	public function testCheckNumber()
	{
		$def = ['code' => 'key', 'type' => 'number', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => '10.25'] ) );
		$this->assertEquals( ['key' => null], $object->check( ['key' => 10.25] ) );
	}


	public function testCheckNumberInvalid()
	{
		$def = ['code' => 'key', 'type' => 'number', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a number'], $object->check( ['key' => 'abc'] ) );
	}


	public function testCheckDate()
	{
		$def = ['code' => 'key', 'type' => 'date', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => '2000-01-01'] ) );
	}


	public function testCheckDateInvalid()
	{
		$def = ['code' => 'key', 'type' => 'date', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a date'], $object->check( ['key' => '01/01/2000'] ) );
	}


	public function testCheckDatetime()
	{
		$def = ['code' => 'key', 'type' => 'datetime', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => '2000-01-01 00:00:00'] ) );
	}


	public function testCheckDatetimeInvalid()
	{
		$def = ['code' => 'key', 'type' => 'datetime', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a date and time'], $object->check( ['key' => '01/01/2000'] ) );
	}


	public function testCheckSelectList()
	{
		$def = ['code' => 'key', 'type' => 'select', 'required' => true, 'default' => ['test' => 'val']];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => 'test'] ) );
	}


	public function testCheckSelectInvalid()
	{
		$def = ['code' => 'key', 'type' => 'select', 'required' => true, 'default' => ['test']];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a listed value'], $object->check( ['key' => 'test2'] ) );
	}


	public function testCheckMap()
	{
		$def = ['code' => 'key', 'type' => 'map', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => null], $object->check( ['key' => ['a' => 'test']] ) );
	}


	public function testCheckMapInvalid()
	{
		$def = ['code' => 'key', 'type' => 'map', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->assertEquals( ['key' => 'Not a key/value map'], $object->check( ['key' => 'test'] ) );
	}


	public function testCheckInvalid()
	{
		$def = ['code' => 'key', 'type' => 'invalid', 'required' => true];
		$criteria = new \Aimeos\MW\Criteria\Attribute\Standard( $def );
		$object = new \Aimeos\MShop\Common\Helper\Config\Standard( ['key' => $criteria] );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$object->check( ['key' => 'abc'] );
	}
}
