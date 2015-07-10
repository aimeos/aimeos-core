<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MW_View_Helper_NavTree_DefaultTest extends MW_Unittest_Testcase
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
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Default( $view, '' );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, TestHelper::getContext()->getConfig() );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

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

		$output = $this->_object->transform( $node, array() );
		$this->assertContains( 'Root', $output );
	}
}
