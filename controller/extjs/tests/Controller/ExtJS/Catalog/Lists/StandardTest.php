<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Catalog_Lists_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Catalog_Lists_Standard( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.list.type.code' => 'unittype1' ) ) ) ),
			'sort' => 'catalog.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'catalog.list.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Base::LEVEL_ONE );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.list.type.domain' => 'product' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$catalogListTypeManager = Controller_ExtJS_Catalog_Lists_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $catalogListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'catalog.list.parentid' => $node->getId(),
				'catalog.list.typeid' => $resultType['items'][0]->{'catalog.list.type.id'},
				'catalog.list.domain' => 'product',
				'catalog.list.refid' => -1,
				'catalog.list.datestart' => '2000-01-01 00:00:00',
				'catalog.list.dateend' => '2000-01-01 00:00:00',
				'catalog.list.config' => array( 'test' => 'unit' ),
				'catalog.list.position' => 1,
				'catalog.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.list.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'catalog.list.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'catalog.list.id'} );
		$this->assertEquals( $saved['items']->{'catalog.list.id'}, $searched['items'][0]->{'catalog.list.id'});
		$this->assertEquals( $saved['items']->{'catalog.list.parentid'}, $searched['items'][0]->{'catalog.list.parentid'});
		$this->assertEquals( $saved['items']->{'catalog.list.typeid'}, $searched['items'][0]->{'catalog.list.typeid'});
		$this->assertEquals( $saved['items']->{'catalog.list.domain'}, $searched['items'][0]->{'catalog.list.domain'});
		$this->assertEquals( $saved['items']->{'catalog.list.refid'}, $searched['items'][0]->{'catalog.list.refid'});
		$this->assertEquals( $saved['items']->{'catalog.list.datestart'}, $searched['items'][0]->{'catalog.list.datestart'});
		$this->assertEquals( $saved['items']->{'catalog.list.dateend'}, $searched['items'][0]->{'catalog.list.dateend'});
		$this->assertEquals( $saved['items']->{'catalog.list.config'}, $searched['items'][0]->{'catalog.list.config'});
		$this->assertEquals( $saved['items']->{'catalog.list.position'}, $searched['items'][0]->{'catalog.list.position'});
		$this->assertEquals( $saved['items']->{'catalog.list.status'}, $searched['items'][0]->{'catalog.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}

}
