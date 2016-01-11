<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JQAdm\Product\Bundle;


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

		$this->object = new \Aimeos\Admin\JQAdm\Product\Bundle\Standard( $this->context, $templatePaths );
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

		$this->assertContains( 'Bundles', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'U:BUNDLE', array( 'product' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
		$this->assertContains( 'Cafe Noire Expresso', $result );
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

		$this->view->item = $manager->findItem( 'U:BUNDLE', array( 'product' ) );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
		$this->assertContains( 'Cafe Noire Expresso', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'U:BUNDLE' );
		$item->setCode( 'jqadm-test-bundle' );
		$item->setId( null );

		$manager->saveItem( $item );


		$param = array(
			'bundle' => array(
				'product.lists.id' => array( '' ),
				'product.lists.refid' => array( $manager->findItem( 'CNE' )->getId() ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'product' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $item->getListItems( 'product' ) ) );
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
