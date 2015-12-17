<?php

namespace Aimeos\Controller\ExtJS\Order\Base\Service\Attribute;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->object = new \Aimeos\Controller\ExtJS\Order\Base\Service\Attribute\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => (object) array( '==' => (object) array( 'order.base.service.attribute.code' => 'REFID' ) ),
					1 => (object) array( '==' => (object) array( 'order.base.service.attribute.editor' => 'core:unittest' ) ),
				)
			),
			'sort' => 'order.base.service.attribute.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'REFID', $result['items'][0]->{'order.base.service.attribute.code'} );
	}

	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$serviceManager = $baseManager->getSubManager( 'service' );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.code', 'OGONE' ) );
		$results = $serviceManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Exception( 'No service item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.service.attribute.parentid' => $expected->getId(),
				'order.base.service.attribute.code' => 'FooBar',
				'order.base.service.attribute.value' => 'ValueTest',
				'order.base.service.attribute.name' => 'TestName'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.service.attribute.code' => 'FooBar' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.service.attribute.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.service.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.id'}, $searched['items'][0]->{'order.base.service.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.ordservid'}, $searched['items'][0]->{'order.base.service.attribute.ordservid'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.code'}, $searched['items'][0]->{'order.base.service.attribute.code'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.name'}, $searched['items'][0]->{'order.base.service.attribute.name'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.value'}, $searched['items'][0]->{'order.base.service.attribute.value'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

