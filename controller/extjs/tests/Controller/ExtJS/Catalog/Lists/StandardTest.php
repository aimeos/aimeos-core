<?php

namespace Aimeos\Controller\ExtJS\Catalog\Lists;


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
		$this->object = new \Aimeos\Controller\ExtJS\Catalog\Lists\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.lists.type.code' => 'unittype1' ) ) ) ),
			'sort' => 'catalog.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'catalog.lists.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$node = $catalogManager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.lists.type.domain' => 'product' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$catalogListTypeManager = \Aimeos\Controller\ExtJS\Catalog\Lists\Type\Factory::createController( \TestHelperExtjs::getContext() );
		$resultType = $catalogListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'catalog.lists.parentid' => $node->getId(),
				'catalog.lists.typeid' => $resultType['items'][0]->{'catalog.lists.type.id'},
				'catalog.lists.domain' => 'product',
				'catalog.lists.refid' => -1,
				'catalog.lists.datestart' => '2000-01-01 00:00:00',
				'catalog.lists.dateend' => '2000-01-01 00:00:00',
				'catalog.lists.config' => array( 'test' => 'unit' ),
				'catalog.lists.position' => 1,
				'catalog.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'catalog.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'catalog.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'catalog.lists.id'} );
		$this->assertEquals( $saved['items']->{'catalog.lists.id'}, $searched['items'][0]->{'catalog.lists.id'});
		$this->assertEquals( $saved['items']->{'catalog.lists.parentid'}, $searched['items'][0]->{'catalog.lists.parentid'});
		$this->assertEquals( $saved['items']->{'catalog.lists.typeid'}, $searched['items'][0]->{'catalog.lists.typeid'});
		$this->assertEquals( $saved['items']->{'catalog.lists.domain'}, $searched['items'][0]->{'catalog.lists.domain'});
		$this->assertEquals( $saved['items']->{'catalog.lists.refid'}, $searched['items'][0]->{'catalog.lists.refid'});
		$this->assertEquals( $saved['items']->{'catalog.lists.datestart'}, $searched['items'][0]->{'catalog.lists.datestart'});
		$this->assertEquals( $saved['items']->{'catalog.lists.dateend'}, $searched['items'][0]->{'catalog.lists.dateend'});
		$this->assertEquals( $saved['items']->{'catalog.lists.config'}, $searched['items'][0]->{'catalog.lists.config'});
		$this->assertEquals( $saved['items']->{'catalog.lists.position'}, $searched['items'][0]->{'catalog.lists.position'});
		$this->assertEquals( $saved['items']->{'catalog.lists.status'}, $searched['items'][0]->{'catalog.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}

}
