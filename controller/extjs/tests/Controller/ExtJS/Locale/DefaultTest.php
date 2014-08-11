<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Locale_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Controller_ExtJS_Locale_Default( TestHelper::getContext() );
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


	public function testAbstractInit()
	{
		$expected = array('success' => true);
		$actual = $this->_object->init( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array('success' => true);
		$actual = $this->_object->finish( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					(object) array( '==' => (object) array( 'locale.siteid' => TestHelper::getContext()->getLocale()->getSiteId() ) ),
					(object) array( '==' => (object) array( 'locale.currencyid' => 'EUR' ) ),
				),
			),
			'sort' => 'locale.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'EUR', $result['items'][0]->{'locale.currencyid'} );
	}


	public function testSaveDeleteItem()
	{

		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'locale.siteid' => TestHelper::getContext()->getLocale()->getSiteId(),
				'locale.currencyid' => 'CHF',
				'locale.languageid' => 'de',
				'locale.status' => 0,
				'locale.position' => 1,
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.currencyid' => 'CHF' ) ) ) ) );

		$saved = $this->_object->saveItems( $saveParam );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'locale.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'locale.id'} );
		$this->assertEquals( $saved['items']->{'locale.id'}, $searched['items'][0]->{'locale.id'} );
		$this->assertEquals( $saved['items']->{'locale.languageid'}, $searched['items'][0]->{'locale.languageid'} );
		$this->assertEquals( $saved['items']->{'locale.currencyid'}, $searched['items'][0]->{'locale.currencyid'} );
		$this->assertEquals( $saved['items']->{'locale.status'}, $searched['items'][0]->{'locale.status'} );
		$this->assertEquals( $saved['items']->{'locale.position'}, $searched['items'][0]->{'locale.position'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testSaveCheckParamsAbstractException()
	{
		$saveParam = (object) array();
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->saveItems( $saveParam );
	}


	public function testAbstractSetLocaleException()
	{
		$saveParam = (object) array(
			'site' => 'badSite',
			'items' => (object) array(),
		);
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->saveItems( $saveParam );
	}

}
