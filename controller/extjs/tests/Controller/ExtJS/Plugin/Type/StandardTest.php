<?php

namespace Aimeos\Controller\ExtJS\Plugin\Type;


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
		$this->object = new \Aimeos\Controller\ExtJS\Plugin\Type\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'plugin.type.code' => 'order' ) ) ) ),
			'sort' => 'plugin.type.code'
		);

		$result = $this->object->searchItems( $params );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No plugin type found' );
		}

		$this->assertEquals( 1, count( $type ) );
		$this->assertEquals( 'order', reset( $type )->{'plugin.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'plugin.type.code' => 'test code',
				'plugin.type.label' => 'test label',
				'plugin.type.status' => 1,
				'plugin.type.domain' => 'plugin'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'plugin.type.code' => 'test code' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'plugin.type.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'plugin.type.id'} );
		$this->assertEquals( $saved['items']->{'plugin.type.id'}, $searched['items'][0]->{'plugin.type.id'} );
		$this->assertEquals( $saved['items']->{'plugin.type.code'}, $searched['items'][0]->{'plugin.type.code'} );
		$this->assertEquals( $saved['items']->{'plugin.type.domain'}, $searched['items'][0]->{'plugin.type.domain'} );
		$this->assertEquals( $saved['items']->{'plugin.type.label'}, $searched['items'][0]->{'plugin.type.label'} );
		$this->assertEquals( $saved['items']->{'plugin.type.status'}, $searched['items'][0]->{'plugin.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
