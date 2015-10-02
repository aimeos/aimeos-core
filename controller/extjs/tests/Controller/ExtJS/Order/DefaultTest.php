<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Order_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Order_Default( TestHelper::getContext() );
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
					0 => array( '~=' => (object) array( 'order.type' => 'web' ) ),
					1 => array( '==' => (object) array( 'order.statusdelivery' => 4 ) ),
					2 => array( '==' => (object) array( 'order.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'order.baseid',
			'dir' => 'DESC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'web', $result['items'][0]->{'order.type'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );
		$results = $baseManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No items found.' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.baseid' => $expected->getId(),
				'order.type' => 'web',
				'order.datepayment' => '2000-01-01 00:00:00',
				'order.datedelivery' => '2001-01-01 00:00:00',
				'order.statuspayment' => 2,
				'order.statusdelivery' => 4,
				'order.relatedid' => 55,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.relatedid' => 55 ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.id'} );
		$this->assertEquals( $saved['items']->{'order.id'}, $searched['items'][0]->{'order.id'} );
		$this->assertEquals( $saved['items']->{'order.baseid'}, $searched['items'][0]->{'order.baseid'} );
		$this->assertEquals( $saved['items']->{'order.type'}, $searched['items'][0]->{'order.type'} );
		$this->assertEquals( $saved['items']->{'order.datepayment'}, $searched['items'][0]->{'order.datepayment'} );
		$this->assertEquals( $saved['items']->{'order.datedelivery'}, $searched['items'][0]->{'order.datedelivery'} );
		$this->assertEquals( $saved['items']->{'order.statuspayment'}, $searched['items'][0]->{'order.statuspayment'} );
		$this->assertEquals( $saved['items']->{'order.statusdelivery'}, $searched['items'][0]->{'order.statusdelivery'} );
		$this->assertEquals( $saved['items']->{'order.relatedid'}, $searched['items'][0]->{'order.relatedid'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

