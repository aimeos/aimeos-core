<?php

namespace Aimeos\Controller\ExtJS\Text;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
		$this->object = \Aimeos\Controller\ExtJS\Text\Factory::createController( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array(
					0 => (object) array( '~=' => (object) array( 'text.content' => 'Cafe Noire Expresso' ) ),
					1 => (object) array( '==' => (object) array( 'text.languageid' => 'de' ) ),
				)
			),
			'sort' => 'text.domain',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'catalog', $result['items'][0]->{'text.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$typeManager = \Aimeos\MShop\Factory::createManager( \TestHelper::getContext(), 'text/type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'text.content' => 'controller test text',
				'text.domain' => 'product',
				'text.typeid' => $type->getId(),
				'text.label' => 'unittest label',
				'text.languageid' => 'de',
				'text.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.content' => 'controller test text' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.id'} );
		$this->assertEquals( $saved['items']->{'text.id'}, $searched['items'][0]->{'text.id'} );
		$this->assertEquals( $saved['items']->{'text.content'}, $searched['items'][0]->{'text.content'} );
		$this->assertEquals( $saved['items']->{'text.domain'}, $searched['items'][0]->{'text.domain'} );
		$this->assertEquals( $saved['items']->{'text.typeid'}, $searched['items'][0]->{'text.typeid'} );
		$this->assertEquals( $saved['items']->{'text.label'}, $searched['items'][0]->{'text.label'} );
		$this->assertEquals( $saved['items']->{'text.languageid'}, $searched['items'][0]->{'text.languageid'} );
		$this->assertEquals( $saved['items']->{'text.status'}, $searched['items'][0]->{'text.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
		$this->assertEquals( 'unittest label', $saved['items']->{'text.label'} );
	}


	public function testSaveItemLabelContent()
	{
		$typeManager = \Aimeos\MShop\Factory::createManager( \TestHelper::getContext(), 'text/type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'text.content' => 'controller test text',
				'text.domain' => 'product',
				'text.typeid' => $type->getId(),
				'text.languageid' => 'de',
				'text.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.content' => 'controller test text' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.id'} );
		$this->assertEquals( $saved['items']->{'text.id'}, $searched['items'][0]->{'text.id'} );
		$this->assertEquals( $saved['items']->{'text.content'}, $searched['items'][0]->{'text.content'} );
		$this->assertEquals( $saved['items']->{'text.domain'}, $searched['items'][0]->{'text.domain'} );
		$this->assertEquals( $saved['items']->{'text.typeid'}, $searched['items'][0]->{'text.typeid'} );
		$this->assertEquals( $saved['items']->{'text.content'}, $searched['items'][0]->{'text.label'} );
		$this->assertEquals( $saved['items']->{'text.languageid'}, $searched['items'][0]->{'text.languageid'} );
		$this->assertEquals( $saved['items']->{'text.status'}, $searched['items'][0]->{'text.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
		$this->assertEquals( 'controller test text', $saved['items']->{'text.label'} );
	}


	public function testFinish()
	{
		$this->object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );
	}
}
