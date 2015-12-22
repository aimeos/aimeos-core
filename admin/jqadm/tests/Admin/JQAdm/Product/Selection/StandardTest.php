<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JQAdm\Product\Selection;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->view = \TestHelperJqadm::getView();
		$this->context = \TestHelperJqadm::getContext();
		$templatePaths = \TestHelperJqadm::getTemplatePaths();

		$this->object = new \Aimeos\Admin\JQAdm\Product\Selection\Standard( $this->context, $templatePaths );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->createItem();
		$result = $this->object->create();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertStringStartsWith( '<div class="product-item-selection', $result );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'U:TEST', array( 'product' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'U:TESTSUB01', $result );
		$this->assertContains( 'U:TESTSUB02', $result );
		$this->assertContains( 'U:TESTSUB03', $result );
		$this->assertContains( 'U:TESTSUB04', $result );
		$this->assertContains( 'U:TESTSUB05', $result );
		$this->assertContains( 'value="30"', $result );
		$this->assertContains( 'value="32"', $result );
	}


	public function testGet()
	{
		$param = array(
			'selection' => array(
				'product.id' => array( 0 => '' ),
				'product.code' => array( 0 => 'testprod' ),
				'product.label' => array( 0 => 'test product' ),
				'attr' => array(
					'id' => array( 0 => '123' ),
					'label' => array( 0 => 'test attribute' ),
					'ref' => array( 0 => 'testprod' ),
				)
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$this->view->item = $manager->findItem( 'U:TEST' );

		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'testprod', $result );
		$this->assertContains( 'value="123"', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'CNC' );
		$item->setCode( 'CNC_copy' );
		$item->setId( null );

		$manager->saveItem( $item );

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem = $attrManager->findItem( 'xs', array(), 'product', 'size' );


		$param = array(
			'selection' => array(
				'product.id' => array( 0 => $item->getId() ),
				'product.code' => array( 0 => 'testprod' ),
				'product.label' => array( 0 => 'test product' ),
				'attr' => array(
					'id' => array( 0 => $attrItem->getId() ),
					'label' => array( 0 => 'test attribute' ),
					'ref' => array( 0 => 'testprod' ),
				)
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'product' ) );
		$variants = $item->getListItems( 'product', 'default' );

		$variant = $manager->getItem( reset( $variants )->getRefId(), array( 'attribute' ) );
		$attributes = $variant->getListItems( 'attribute', 'variant' );

		$manager->deleteItems( array( $item->getId(), $variant->getId() ) );


		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $variants ) );
		$this->assertEquals( 1, count( $attributes ) );
		$this->assertEquals( 'testprod', $variant->getCode() );
		$this->assertEquals( $attrItem->getId(), reset( $attributes )->getRefId() );
	}
}
