<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Stage_Navigator_DefaultTest extends MW_Unittest_Testcase
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

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Catalog_Stage_Navigator_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Stage_Navigator_Default( $context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'l-pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$view->navigationPrev = '#';
		$view->navigationNext = '#';

		$output = $this->_object->getHeader();

		$this->assertContains( '<link rel="prev"', $output );
		$this->assertContains( '<link rel="next prefetch"', $output );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'l-pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$view->navigationPrev = '#';
		$view->navigationNext = '#';

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<!-- catalog.stage.navigator -->', $output );
		$this->assertContains( '<a class="prev"', $output );
		$this->assertContains( '<a class="next"', $output );
	}


	public function testModifyHeader()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'l-pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->_object->modifyHeader( $content, 1 );

		$this->assertContains( '<!-- catalog.stage.navigator -->', $output );
	}


	public function testModifyBody()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'l-pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->_object->modifyBody( $content, 1 );

		$this->assertContains( '<div class="catalog-stage-navigator">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->_object->process();
	}
}
