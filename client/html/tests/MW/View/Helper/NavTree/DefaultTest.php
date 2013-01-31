<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: NavTreeTest.php 1320 2012-10-19 19:57:38Z nsendetzky $
 */

class MW_View_Helper_NavTree_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MW_View_Helper_NavTree_DefaultTest');
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
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Default( $view, '' );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, array() );
		$view->addHelper( 'config', $helper );

		$this->_object = new MW_View_Helper_NavTree_Default( $view );
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


	public function testTransform()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$output = $this->_object->transform( $node );
		$this->assertContains( 'Root', $output );
	}
}
