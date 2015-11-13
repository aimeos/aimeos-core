<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Coupon;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Controller\JsonAdm\Coupon\Standard( $this->context, $this->view, $templatePaths, 'coupon' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'coupon.code.code' => '90AB' )
			),
			'include' => 'coupon/code'
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
		$this->assertEquals( 'coupon', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['coupon/code'] ) );
		$this->assertEquals( 1, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'coupon' => 'coupon.id,coupon.label'
			),
			'sort' => 'coupon.id',
			'include' => 'coupon/code'
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 5, $result['meta']['total'] );
		$this->assertEquals( 5, count( $result['data'] ) );
		$this->assertEquals( 'coupon', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['coupon/code'] ) );
		$this->assertEquals( 5, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}