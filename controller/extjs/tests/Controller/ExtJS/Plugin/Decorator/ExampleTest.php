<?php

namespace Aimeos\Controller\ExtJS\Plugin\Decorator;


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
		$controller = \Aimeos\Controller\ExtJS\Plugin\Factory::createController( $context );
		$this->object = new \Aimeos\Controller\ExtJS\Plugin\Decorator\Example( $controller, $context );
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


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'plugin.provider' => 'Shipping' ) ) ) ),


			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		if( ( $plugin = reset( $result ) ) === false ) {
			throw new \Exception( 'No plugin found' );
		}

		$this->assertEquals( 1, count( $plugin ) );
		$this->assertEquals( reset( $plugin )->{'plugin.provider'}, 'Shipping,Example' );
	}


	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'plugin.type.code', 'order' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No plugin type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'plugin.provider' => 'test provider',
				'plugin.typeid' => $type->getId(),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'plugin.provider' => 'test provider' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'plugin.id'} );
		$this->object->deleteItems( $deleteParams );
		$this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->object->getServiceDescription();

		$this->assertArrayHasKey( 'Plugin.init', $actual );
	}


	public function testGetSearchSchema()
	{
		$actual = $this->object->getSearchSchema();

		$this->assertArrayHasKey( 'criteria', $actual );
	}


	public function testGetItemSchema()
	{
		$actual = $this->object->getItemSchema();

		$this->assertArrayHasKey( 'name', $actual );
		$this->assertArrayHasKey( 'properties', $actual );
	}


	public function testCall()
	{
		$result = $this->object->__call( 'getServiceDescription', array() );
		$this->assertInternalType( 'array', $result );
	}

}
