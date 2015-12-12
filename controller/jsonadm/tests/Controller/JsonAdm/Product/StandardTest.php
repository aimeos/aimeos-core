<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Product;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Controller\JsonAdm\Product\Standard( $this->context, $this->view, $templatePaths, 'product' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'product.code' => 'CNE' )
			),
			'include' => 'text,product,product/property,product/stock'
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
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 6, count( $result['data'][0]['relationships']['text'] ) );
		$this->assertEquals( 5, count( $result['data'][0]['relationships']['product'] ) );
		$this->assertEquals( 4, count( $result['data'][0]['relationships']['product/property'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['product/stock'] ) );
		$this->assertEquals( 15, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'product' => 'product.id,product.label'
			),
			'sort' => 'product.id',
			'include' => 'product,product/stock'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 28, $result['meta']['total'] );
		$this->assertEquals( 25, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 5, count( $result['data'][0]['relationships']['product'] ) );
		$this->assertEquals( 27, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}