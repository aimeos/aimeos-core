<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Locale_Select_Currency_StandardTest extends PHPUnit_Framework_TestCase
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
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Locale_Select_Currency_Standard( TestHelper::getContext(), $paths );
		$this->object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( 0, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->selectCurrencyId = 'EUR';
		$view->selectLanguageId = 'de';
		$view->selectMap = array(
			'de' => array(
				'EUR' => array( 'loc_languageid' => 'de', 'loc_currencyid' => 'EUR' ),
				'CHF' => array( 'loc_languageid' => 'de', 'loc_currencyid' => 'CHF' ),
			),
			'en' => array( 'USD' => array( 'loc_languageid' => 'en', 'loc_currencyid' => 'USD' ) ),
		);

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<div class="locale-select-currency">', $output );
		$this->assertContains( '<li class="select-dropdown select-current"><a href="#">EUR', $output );
		$this->assertContains( '<li class="select-item active">', $output );

		$this->assertEquals( 0, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
