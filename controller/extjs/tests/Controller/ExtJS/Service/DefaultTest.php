<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Service_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Service_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'service.label' => 'unitlabel' ) ) ) ),
			'sort' => 'service.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'No service found' );
		}

		$this->assertEquals( 1, count( $service ) );
		$this->assertEquals( reset( $service )->{'service.label'}, 'unitlabel');
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.type.code', 'delivery' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No service type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'service.position' => 1,
				'service.label' => 'test service',
				'service.status' => 1,
				'service.code' => 'test code',
				'service.provider' => 'test provider',
				'service.config' => array( 'url' => 'www.url.de' ),
				'service.typeid' => $type->getId(),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'service.code' => 'test code' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.id'} );
		$this->assertEquals( $saved['items']->{'service.id'}, $searched['items'][0]->{'service.id'} );
		$this->assertEquals( $saved['items']->{'service.position'}, $searched['items'][0]->{'service.position'} );
		$this->assertEquals( $saved['items']->{'service.label'}, $searched['items'][0]->{'service.label'} );
		$this->assertEquals( $saved['items']->{'service.status'}, $searched['items'][0]->{'service.status'} );
		$this->assertEquals( $saved['items']->{'service.code'}, $searched['items'][0]->{'service.code'} );
		$this->assertEquals( $saved['items']->{'service.provider'}, $searched['items'][0]->{'service.provider'} );
		$this->assertEquals( $saved['items']->{'service.config'}, $searched['items'][0]->{'service.config'} );
		$this->assertEquals( $saved['items']->{'service.typeid'}, $searched['items'][0]->{'service.typeid'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testFinish()
	{
		$result = $this->_object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}
