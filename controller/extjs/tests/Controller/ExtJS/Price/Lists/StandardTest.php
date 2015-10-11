<?php

namespace Aimeos\Controller\ExtJS\Price\Lists;


/**
 * @copyright Metaways Infosystems GmbH, 2012
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
		$this->object = new \Aimeos\Controller\ExtJS\Price\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.lists.domain' => 'customer' ) ) ) ),
			'sort' => 'price.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'customer', $result['items'][0]->{'price.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Customer']['items'] ) );
		$this->assertEquals( 'UTC001', $result['graph']['Customer']['items'][0]->{'customer.code'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1, 'price.lists.type.domain' => 'customer' );
		$priceManager = new \Aimeos\Controller\ExtJS\Price\Standard( \TestHelper::getContext() );
		$result = $priceManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.lists.type.domain' => 'customer' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$priceListTypeManager = \Aimeos\Controller\ExtJS\Price\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $priceListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'price.lists.parentid' => $result['items'][0]->{'price.id'},
				'price.lists.typeid' => $resultType['items'][0]->{'price.lists.type.id'},
				'price.lists.domain' => 'customer',
				'price.lists.refid' => -1,
				'price.lists.datestart' => '2000-01-01 00:00:00',
				'price.lists.dateend' => '2001-01-01 00:00:00',
				'price.lists.config' => array( 'test' => 'unit' ),
				'price.lists.position' => 1,
				'price.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.lists.refid' => -1 ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'price.lists.id'} );
		$this->assertEquals( $saved['items']->{'price.lists.id'}, $searched['items'][0]->{'price.lists.id'});
		$this->assertEquals( $saved['items']->{'price.lists.parentid'}, $searched['items'][0]->{'price.lists.parentid'});
		$this->assertEquals( $saved['items']->{'price.lists.typeid'}, $searched['items'][0]->{'price.lists.typeid'});
		$this->assertEquals( $saved['items']->{'price.lists.domain'}, $searched['items'][0]->{'price.lists.domain'});
		$this->assertEquals( $saved['items']->{'price.lists.refid'}, $searched['items'][0]->{'price.lists.refid'});
		$this->assertEquals( $saved['items']->{'price.lists.datestart'}, $searched['items'][0]->{'price.lists.datestart'});
		$this->assertEquals( $saved['items']->{'price.lists.dateend'}, $searched['items'][0]->{'price.lists.dateend'});
		$this->assertEquals( $saved['items']->{'price.lists.config'}, $searched['items'][0]->{'price.lists.config'});
		$this->assertEquals( $saved['items']->{'price.lists.position'}, $searched['items'][0]->{'price.lists.position'});
		$this->assertEquals( $saved['items']->{'price.lists.status'}, $searched['items'][0]->{'price.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
