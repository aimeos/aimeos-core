<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Common\Item\Helper\Form;


/**
 * Test class for \Aimeos\MShop\Common\Item\Helper\Form\Standard
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
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
			'language' => new \Aimeos\MW\Criteria\Attribute\Standard( array(
				'code' => 'domain',
				'internalcode' => 'domain',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Domain',
				'default' => 'testDomain',
			) ),
		);

		$this->object = new \Aimeos\MShop\Common\Item\Helper\Form\Standard( 'http://www.example.com', 'post', $this->values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
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

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Helper\Form\Iface', $return );
		$this->assertEquals( false, $this->object->getExternal() );
	}

	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.example.com', $this->object->getUrl() );
	}

	public function testSetUrl()
	{
		$return = $this->object->setUrl( 'http://www.example.de' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Helper\Form\Iface', $return );
		$this->assertEquals( 'http://www.example.de', $this->object->getUrl() );
	}

	public function testGetMethod()
	{
		$this->assertEquals( 'post', $this->object->getMethod() );
	}

	public function testSetMethod()
	{
		$return = $this->object->setMethod( 'get' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Helper\Form\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Helper\Form\Iface', $return );
		$this->assertEquals( 'test', $this->object->getValue( 'name' )->getDefault() );
	}
}
