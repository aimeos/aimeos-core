<?php

namespace Aimeos\Admin\JQAdm\Product;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->view = \TestHelperJqadm::getView();
		$request = $this->getMock( '\Psr\Http\Message\ServerRequestInterface' );
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view ->addHelper( 'request', $helper );

		$this->context = \TestHelperJqadm::getContext();
		$templatePaths = \TestHelperJqadm::getTemplatePaths();

		$this->object = new \Aimeos\Admin\JQAdm\Product\Standard( $this->context, $templatePaths );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreate()
	{
		$this->object->create();
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$param = array( 'id' => $manager->findItem( 'CNC' )->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->copy();

		$this->assertContains( 'CNC_copy', $result );
	}


	public function testDelete()
	{
		$this->assertNull( $this->object->delete() );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$param = array( 'id' => $manager->findItem( 'CNC' )->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->get();

		$this->assertContains( 'CNC', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product/type' );

		$search = $typeManager->createSearch();
		$search->setSlice( 0, 1 );
		$typeItems = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $typeItems ) ) === false ) {
			throw new \Exception( 'No product type item found' );
		}


		$param = array(
			'item' => array(
				'product.id' => '',
				'product.typeid' => $typeItem->getId(),
				'product.code' => 'test',
				'product.label' => 'test label',
				'product.datestart' => null,
				'product.dateend' => null,
				'config' => array(
					'key' => array( 0 => 'test key' ),
					'val' => array( 0 => 'test value' ),
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->save();

		$manager->deleteItem( $manager->findItem( 'test' )->getId() );
	}


	public function testSearch()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'site' => 'unittest', 'lang' => 'de' ) );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->search();

		$this->assertContains( 'CNE', $result );
	}
}
