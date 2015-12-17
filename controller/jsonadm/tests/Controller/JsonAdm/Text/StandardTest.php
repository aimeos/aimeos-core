<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Text;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getControllerPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Controller\JsonAdm\Text\Standard( $this->context, $this->view, $templatePaths, 'text' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'text.label' => 'misc_long_desc' )
			),
			'include' => 'media'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'text', $result['data'][0]['type'] );
		$this->assertEquals( 4, count( $result['data'][0]['relationships']['media'] ) );
		$this->assertEquals( 4, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}