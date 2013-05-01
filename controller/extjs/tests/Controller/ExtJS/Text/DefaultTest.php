<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


class Controller_ExtJS_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Text_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = Controller_ExtJS_Text_Factory::createController( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'text.content' => 'Cafe Noire Expresso' ) ) ) ),
			'sort' => 'text.domain',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'catalog', $result['items'][0]->{'text.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Text_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No type item found' );
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

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

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
		$this->assertEquals('unittest label', $saved['items']->{'text.label'} );
	}

}
