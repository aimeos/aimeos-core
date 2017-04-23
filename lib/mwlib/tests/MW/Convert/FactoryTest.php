<?php

namespace Aimeos\MW\Convert;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateConverter()
	{
		$object = \Aimeos\MW\Convert\Factory::createConverter( 'Text/LatinUTF8' );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Convert\\Iface', $object );
	}


	public function testCreateConverterCompose()
	{
		$object = \Aimeos\MW\Convert\Factory::createConverter( array( 'Text/LatinUTF8', 'DateTime/EnglishISO' ) );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Convert\\Iface', $object );
	}


	public function testCreateConverterInvalidName()
	{
		$this->expectException( '\\Aimeos\\MW\\Convert\\Exception' );
		\Aimeos\MW\Convert\Factory::createConverter( '$' );
	}


	public function testCreateConverterInvalidClass()
	{
		$this->expectException( '\\Aimeos\\MW\\Convert\\Exception' );
		\Aimeos\MW\Convert\Factory::createConverter( 'Test/Invalid' );
	}


	public function testCreateConverterInvalidInterface()
	{
		$this->expectException( '\\Aimeos\\MW\\Convert\\Exception' );
		\Aimeos\MW\Convert\Factory::createConverter( 'TestConvert' );
	}
}


class TestConvert
{
}
