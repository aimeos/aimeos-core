<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Price;


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

		$this->object = new \Aimeos\Controller\JsonAdm\Media\Standard( $this->context, $this->view, $templatePaths, 'price' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'price.value' => '12.95' )
			),
			'include' => 'attribute'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertEquals( 2, count( $result['data'] ) );
		$this->assertEquals( 'price', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['data'][0]['relationships'] ) );
		$this->assertEquals( 0, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}