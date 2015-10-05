<?php

namespace Aimeos\Client\Html\Catalog\Stage\Navigator;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
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
		$context = \TestHelper::getContext();
		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Navigator\Standard( $context, $paths );
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
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'l_pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$view->navigationPrev = '#';
		$view->navigationNext = '#';

		$output = $this->object->getHeader();

		$this->assertContains( '<link rel="prev"', $output );
		$this->assertContains( '<link rel="next prefetch"', $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'l_pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$view->navigationPrev = '#';
		$view->navigationNext = '#';

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<!-- catalog.stage.navigator -->', $output );
		$this->assertContains( '<a class="prev"', $output );
		$this->assertContains( '<a class="next"', $output );
	}


	public function testModifyHeader()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'l_pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->object->modifyHeader( $content, 1 );

		$this->assertContains( '<!-- catalog.stage.navigator -->', $output );
	}


	public function testModifyBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'l_pos' => 1 ) );
		$view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->object->modifyBody( $content, 1 );

		$this->assertContains( '<div class="catalog-stage-navigator">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
