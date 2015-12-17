<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Order;


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

		$this->object = new \Aimeos\Controller\JsonAdm\Order\Standard( $this->context, $this->view, $templatePaths, 'order' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.datepayment' => '2008-02-15 12:34:56' )
			),
			'include' => 'order/base,order/status'
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
		$this->assertEquals( 'order', $result['data'][0]['type'] );
		$this->assertEquals( 3, count( $result['data'][0]['relationships']['order/status'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['order/base'] ) );
		$this->assertEquals( 4, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'order' => 'order.id,order.type'
			),
			'sort' => 'order.id',
			'include' => 'order/status'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertGreaterThanOrEqual( 4, $result['meta']['total'] );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'] ) );
		$this->assertEquals( 'order', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertGreaterThanOrEqual( 3, count( $result['data'][0]['relationships']['order/status'] ) );
		$this->assertGreaterThanOrEqual( 11, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}