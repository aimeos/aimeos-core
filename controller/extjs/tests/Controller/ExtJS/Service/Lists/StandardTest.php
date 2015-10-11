<?php

namespace Aimeos\Controller\ExtJS\Service\Lists;


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
		$this->object = new \Aimeos\Controller\ExtJS\Service\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.lists.type.code' => 'unittype1' ) ) ) ),
			'sort' => 'service.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'service.lists.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( \TestHelper::getContext() );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.label', 'unitlabel' ) );
		$resultService = $serviceManager->searchItems( $search );

		if( ( $item = reset( $resultService ) ) === false ) {
			throw new \Exception( 'No service item found' );
		}

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.lists.type.domain' => 'product' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$serviceListTypeManager = \Aimeos\Controller\ExtJS\Service\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $serviceListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.lists.parentid' => $item->getId(),
				'service.lists.typeid' => $resultType['items'][0]->{'service.lists.type.id'},
				'service.lists.domain' => 'product',
				'service.lists.refid' => -1,
				'service.lists.datestart' => '2000-01-01 00:00:00',
				'service.lists.dateend' => '2001-01-01 00:00:00',
				'service.lists.config' => array( 'test' => 'unit' ),
				'service.lists.position' => 1,
				'service.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.lists.id'} );
		$this->assertEquals( $saved['items']->{'service.lists.id'}, $searched['items'][0]->{'service.lists.id'});
		$this->assertEquals( $saved['items']->{'service.lists.parentid'}, $searched['items'][0]->{'service.lists.parentid'});
		$this->assertEquals( $saved['items']->{'service.lists.typeid'}, $searched['items'][0]->{'service.lists.typeid'});
		$this->assertEquals( $saved['items']->{'service.lists.domain'}, $searched['items'][0]->{'service.lists.domain'});
		$this->assertEquals( $saved['items']->{'service.lists.refid'}, $searched['items'][0]->{'service.lists.refid'});
		$this->assertEquals( $saved['items']->{'service.lists.datestart'}, $searched['items'][0]->{'service.lists.datestart'});
		$this->assertEquals( $saved['items']->{'service.lists.dateend'}, $searched['items'][0]->{'service.lists.dateend'});
		$this->assertEquals( $saved['items']->{'service.lists.config'}, $searched['items'][0]->{'service.lists.config'});
		$this->assertEquals( $saved['items']->{'service.lists.position'}, $searched['items'][0]->{'service.lists.position'});
		$this->assertEquals( $saved['items']->{'service.lists.status'}, $searched['items'][0]->{'service.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
