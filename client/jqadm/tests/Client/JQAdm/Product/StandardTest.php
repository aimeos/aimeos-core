<?php

namespace Aimeos\Client\JQAdm\Product;


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
		$this->view = \TestHelper::getView();
		$this->context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getJQAdmTemplatePaths();

		$this->object = new \Aimeos\Client\JQAdm\Product\Standard( $this->context, $templatePaths );
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

		$param = array( 'id' => $manager->getItem( 'CNC' )->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->copy();
	}


	public function testDelete()
	{
		$this->object->delete();
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$param = array( 'id' => $manager->getItem( 'CNC' )->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->get();
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
			),
			'product.config' => array(
				'key' => array( 0 => 'test key' ),
				'val' => array( 0 => 'test value' ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		echo $this->object->save();

		try {
			$manager->deleteItem( $manager->getItem( 'test' )->getId() );
		} catch( \MShop_Exception $e ) {
			$this->markTestFailed( 'Item was not saved' );
		}
	}


	public function testSearch()
	{
		$this->object->search();
	}
}
