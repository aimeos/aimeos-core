<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Common\Helper\Form;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'name' => new \Aimeos\MW\Criteria\Attribute\Standard( array(
				'code' => 'name',
				'internalcode' => 'name',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Name',
				'default' => 'unittest',
			) ),
			'site' => new \Aimeos\MW\Criteria\Attribute\Standard( array(
				'code' => 'site',
				'internalcode' => 'site',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Site',
				'default' => 'de',
			) ),
			'language' => new \Aimeos\MW\Criteria\Attribute\Standard( array(
				'code' => 'language',
				'internalcode' => 'language',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Language',
				'default' => 'de',
			) ),
			'domain' => new \Aimeos\MW\Criteria\Attribute\Standard( array(
				'code' => 'domain',
				'internalcode' => 'domain',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Domain',
				'default' => 'testDomain',
			) ),
		);

		$this->object = new \Aimeos\MShop\Common\Helper\Form\Standard( 'http://www.example.com', 'post', $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->values );
	}


	public function testGetExternal()
	{
		$this->assertEquals( true, $this->object->getExternal() );
	}


	public function testSetExternal()
	{
		$return = $this->object->setExternal( false );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $return );
		$this->assertEquals( false, $this->object->getExternal() );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.example.com', $this->object->getUrl() );
	}


	public function testSetUrl()
	{
		$return = $this->object->setUrl( 'http://www.example.de' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $return );
		$this->assertEquals( 'http://www.example.de', $this->object->getUrl() );
	}


	public function testGetMethod()
	{
		$this->assertEquals( 'post', $this->object->getMethod() );
	}


	public function testSetMethod()
	{
		$return = $this->object->setMethod( 'get' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $return );
		$this->assertEquals( 'get', $this->object->getMethod() );
	}


	public function testGetValues()
	{
		$this->assertEquals( $this->values, $this->object->getValues() );
	}


	public function testGetValue()
	{
		$this->assertEquals( 'unittest', $this->object->getValue( 'name' )->getDefault() );
	}


	public function testSetValue()
	{
		$item = new \Aimeos\MW\Criteria\Attribute\Standard( array(
			'code' => 'name',
			'internalcode' => 'name',
			'type' => 'string',
			'internaltype' => 'string',
			'label' => 'Name',
			'default' => 'test',
		) );

		$return = $this->object->setValue( 'name', $item );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getValue( 'name' )->getDefault() );
	}
}
