<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JQAdm\Product\Price;


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

		$this->object = new \Aimeos\Admin\JQAdm\Product\Price\Standard( $this->context, $templatePaths );
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

		$this->assertContains( 'Prices', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'CNC', array( 'price' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="for 1 product"', $result );
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

		$this->view->item = $manager->findItem( 'CNC', array( 'price' ) );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="for 1 product"', $result );
	}


	public function testSave()
	{
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'price/type' );
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'CNC' );
		$item->setCode( 'jqadm-test' );
		$item->setId( null );

		$manager->saveItem( $item );


		$param = array(
			'price' => array(
				'product.lists.id' => array( '' ),
				'price.typeid' => array( $typeManager->findItem( 'default', array(), 'product' )->getId() ),
				'price.currencyid' => array( 'EUR' ),
				'price.label' => array( 'test' ),
				'price.quantity' => array( '2' ),
				'price.value' => array( '10.00' ),
				'price.costs' => array( '1.00' ),
				'price.rebate' => array( '5.00' ),
				'price.taxrate' => array( '20.00' ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'price' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $item->getListItems() ) );

		foreach( $item->getListItems( 'price' ) as $listItem )
		{
			$this->assertEquals( 'price', $listItem->getDomain() );
			$this->assertEquals( 'default', $listItem->getType() );

			$refItem = $listItem->getRefItem();
			$this->assertEquals( 'default', $refItem->getType() );
			$this->assertEquals( 'EUR', $refItem->getCurrencyId() );
			$this->assertEquals( 'test', $refItem->getLabel() );
			$this->assertEquals( '2', $refItem->getQuantity() );
			$this->assertEquals( '10.00', $refItem->getValue() );
			$this->assertEquals( '1.00', $refItem->getCosts() );
			$this->assertEquals( '5.00', $refItem->getRebate() );
			$this->assertEquals( '20.00', $refItem->getTaxRate() );
		}
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
