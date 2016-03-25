<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Admin\JQAdm\Product\Characteristic\Property;


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

		$this->object = new \Aimeos\Admin\JQAdm\Product\Characteristic\Property\Standard( $this->context, $templatePaths );
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

		$this->assertContains( 'Properties', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'CNC' );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Properties', $result );
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
		$this->assertContains( 'Properties', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$propManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product/property' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product/property/type' );

		$item = $manager->findItem( 'CNC' );
		$item->setCode( 'jqadm-test-property' );
		$item->setId( null );

		$manager->saveItem( $item );


		$typeid = $typeManager->findItem( 'package-height', array(), 'product/property' )->getId();

		$param = array(
			'characteristic' => array(
				'property' => array(
					'product.property.id' => array( '' ),
					'product.property.typeid' => array( $typeid ),
					'product.property.value' => array( '10.0' ),
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$search = $propManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $item->getId() ) );
		$items = $propManager->searchItems( $search );

		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( null, reset( $items )->getLanguageId() );
		$this->assertEquals( '10.0', reset( $items )->getValue() );
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
