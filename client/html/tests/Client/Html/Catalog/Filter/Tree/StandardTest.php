<?php

namespace Aimeos\Client\Html\Catalog\Filter\Tree;


/**
 * @copyright Metaways Infosystems GmbH, 2012
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
		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Tree\Standard( \TestHelper::getContext(), $paths );
		$this->object->setView( \TestHelper::getView() );
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
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetBody()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getChild( 1 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertContains( 'Groups', $output );
		$this->assertContains( 'Neu', $output );
		$this->assertContains( 'level-2', $output );

		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testGetBodyLevelsAlways()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$view = $this->object->getView();

		$conf = new \Aimeos\MW\Config\PHPArray( array( 'client' => array( 'html' => array( 'catalog' => array( 'filter' => array( 'tree' => array( 'levels-always' => 2 ) ) ) ) ) ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 8, count( $tags ) );
	}


	public function testGetBodyLevelsOnly()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$view = $this->object->getView();

		$conf = new \Aimeos\MW\Config\PHPArray( array( 'client' => array( 'html' => array( 'catalog' => array( 'filter' => array( 'tree' => array( 'levels-only' => 1 ) ) ) ) ) ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getChild( 0 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertNotContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
