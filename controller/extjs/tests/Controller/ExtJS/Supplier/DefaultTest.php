<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Supplier_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->_object = new Controller_ExtJS_Supplier_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'supplier.label' => 'unitSupplier001' ) ) ) ),
			'sort' => 'supplier.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unitSupplier001', $result['items'][0]->{'supplier.label'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'supplier.label' => 'controller test supplier',
				'supplier.status' => 1,
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.label' => 'controller test supplier' ) ) ) ) );

		$saved = $this->_object->saveItems( $saveParam );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'supplier.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'supplier.id'} );
		$this->assertEquals( $saved['items']->{'supplier.id'}, $searched['items'][0]->{'supplier.id'} );
		$this->assertEquals( $saved['items']->{'supplier.label'}, $searched['items'][0]->{'supplier.label'} );
		$this->assertEquals( $saved['items']->{'supplier.status'}, $searched['items'][0]->{'supplier.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testFinish()
	{
		$result = $this->_object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}
