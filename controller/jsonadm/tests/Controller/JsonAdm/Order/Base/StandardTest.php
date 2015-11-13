<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Order\Base;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Controller\JsonAdm\Order\Base\Standard( $this->context, $this->view, $templatePaths, 'order/base' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.base.price' => '4800.00' )
			),
			'include' => 'order/base/address,order/base/product'
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'order/base', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['order/base/address'] ) );
		$this->assertEquals( 6, count( $result['data'][0]['relationships']['order/base/product'] ) );
		$this->assertEquals( 7, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'order/base' => 'order.base.languageid,order.base.currencyid'
			),
			'sort' => 'order.base.id',
			'include' => 'order/base/product'
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertGreaterThanOrEqual( 4, $result['meta']['total'] );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'] ) );
		$this->assertEquals( 'order/base', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'][0]['relationships']['order/base/product'] ) );
		$this->assertGreaterThanOrEqual( 14, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}