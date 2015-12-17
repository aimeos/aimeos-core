<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Customer;


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

		$this->object = new \Aimeos\Controller\JsonAdm\Customer\Standard( $this->context, $this->view, $templatePaths, 'customer' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'customer.code' => 'UTC001' )
			),
			'include' => 'text,customer/address'
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
		$this->assertEquals( 'customer', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['text'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['customer/address'] ) );
		$this->assertEquals( 2, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'customer' => 'customer.id,customer.label'
			),
			'sort' => 'customer.id',
			'include' => 'customer/address'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$header = array();
		$status = 500;

		$result = json_decode( $this->object->get( '', $header, $status ), true );

		$this->assertEquals( 200, $status );
		$this->assertEquals( 1, count( $header ) );
		$this->assertEquals( 3, $result['meta']['total'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertEquals( 'customer', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['customer/address'] ) );
		$this->assertEquals( 4, count( $result['included'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}