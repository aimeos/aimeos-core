<?php

namespace Aimeos\Controller\ExtJS\Service\Type;


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
		$this->object = new \Aimeos\Controller\ExtJS\Service\Type\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.type.code' => 'delivery' ) ) ) ),
			'sort' => 'service.type.code'
		);

		$result = $this->object->searchItems( $params );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No service type found' );
		}

		$this->assertEquals( 1, count( $type ) );
		$this->assertEquals( 'delivery', reset( $type )->{'service.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.type.code' => 'test code',
				'service.type.label' => 'test label',
				'service.type.status' => 1,
				'service.type.domain' => 'service'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.type.code' => 'test code' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.type.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.type.id'} );
		$this->assertEquals( $saved['items']->{'service.type.id'}, $searched['items'][0]->{'service.type.id'} );
		$this->assertEquals( $saved['items']->{'service.type.code'}, $searched['items'][0]->{'service.type.code'} );
		$this->assertEquals( $saved['items']->{'service.type.domain'}, $searched['items'][0]->{'service.type.domain'} );
		$this->assertEquals( $saved['items']->{'service.type.label'}, $searched['items'][0]->{'service.type.label'} );
		$this->assertEquals( $saved['items']->{'service.type.status'}, $searched['items'][0]->{'service.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
