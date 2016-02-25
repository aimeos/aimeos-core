<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JQAdm\Product\Category;


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

		$this->object = new \Aimeos\Admin\JQAdm\Product\Category\Standard( $this->context, $templatePaths );
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

		$this->assertContains( 'Categories', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'CNC' );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="Internet"', $result );
		$this->assertContains( 'value="Neu"', $result );
		$this->assertContains( 'value="Kaffee"', $result );
	}


	public function testDelete()
	{
		$result = $this->object->delete();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'CNC' );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="Internet"', $result );
		$this->assertContains( 'value="Neu"', $result );
		$this->assertContains( 'value="Kaffee"', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'catalog' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'catalog/lists/type' );
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'root' );
		$item->setCode( 'jqadm-test-root' );
		$item->setId( null );

		$manager->insertItem( $item );


		$typeid = $typeManager->findItem( 'default', array(), 'product' )->getId();

		$param = array(
			'category' => array(
				'catalog.lists.id' => array( '' ),
				'catalog.lists.typeid' => array( $typeid ),
				'catalog.id' => array( $item->getId() ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $productManager->createItem();

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'product' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $item->getListItems( 'product' ) ) );
	}


	public function testSavePromotion()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'catalog' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'catalog/lists/type' );
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'root' );
		$item->setCode( 'jqadm-test-root' );
		$item->setId( null );

		$manager->insertItem( $item );


		$typeid = $typeManager->findItem( 'promotion', array(), 'product' )->getId();

		$param = array(
			'category' => array(
				'catalog.lists.id' => array( '' ),
				'catalog.lists.typeid' => array( $typeid ),
				'catalog.id' => array( $item->getId() ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $productManager->createItem();

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'product' ) );
		$listItems = $item->getListItems( 'product' );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $listItems ) );
		$this->assertEquals( $typeid, reset( $listItems )->getTypeId() );
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
