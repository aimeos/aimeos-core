<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Filter_Tree_DefaultTest extends MW_Unittest_Testcase
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
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Filter_Tree_Default( TestHelper::getContext(), $paths );
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
		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetBody()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );

		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $node->getChild( 1 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertContains( 'Groups', $output );
		$this->assertContains( 'Neu', $output );
		$this->assertContains( 'level-2', $output );

		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testGetBodyLevelsAlways()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$view = $this->_object->getView();

		$conf = new MW_Config_Array( array( 'client' => array( 'html' => array( 'catalog' => array( 'filter' => array( 'tree' => array( 'levels-always' => 2 ) ) ) ) ) ) );
		$helper = new MW_View_Helper_Config_Default( $view, $conf );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $node->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 8, count( $tags ) );
	}


	public function testGetBodyLevelsOnly()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_TREE );

		$view = $this->_object->getView();

		$conf = new MW_Config_Array( array( 'client' => array( 'html' => array( 'catalog' => array( 'filter' => array( 'tree' => array( 'levels-only' => 1 ) ) ) ) ) ) );
		$helper = new MW_View_Helper_Config_Default( $view, $conf );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $node->getChild( 0 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertNotContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}

}
