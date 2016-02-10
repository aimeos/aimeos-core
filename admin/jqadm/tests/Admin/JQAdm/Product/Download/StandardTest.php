<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Admin\JQAdm\Product\Download;


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

		$this->object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Product\Download\Standard' )
			->setConstructorArgs( array( $this->context, $templatePaths ) )
			->setMethods( array( 'storeFile' ) )
			->getMock();
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

		$this->assertContains( 'Downloads', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'CNE', array( 'attribute' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Test URL', $result );
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

		$this->view->item = $manager->findItem( 'CNE', array( 'attribute' ) );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'Test URL', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'CNE' );
		$item->setCode( 'jqadm-test-download' );
		$item->setId( null );

		$manager->saveItem( $item );


		$param = array(
			'download' => array(
				'product.lists.id' => array( '' ),
				'attribute.status' => array( 1 ),
				'attribute.label' => array( 'test' ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$files = array(
			'download' => array(
				'tmp_name' => array( 'files' => array( 0 => '' ) ),
				'name' => array( 'files' => array( '' ) ),
				'type' => array( 'files' => array( '' ) ),
				'size' => array( 'files' => array( 0 ) ),
				'error' => array( 'files' => array( 0 ) )
			),
		);

		$helper = $this->getMockBuilder( '\Aimeos\MW\View\Helper\Request\Standard' )
			->setConstructorArgs( array( $this->view, '', '', null, $files ) )
			->setMethods( array( 'checkUploadedFile' ) )
			->getMock();
		$this->view->addHelper( 'request', $helper );
		$this->view->item = $item;

		$this->object->expects( $this->once() )->method( 'storeFile' )
			->will( $this->returnValue( 'test/file.ext' ) );

		$attributeStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Attribute\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();
		$attributeStub->expects( $this->once() )->method( 'saveItem' );
		\Aimeos\MShop\Factory::injectManager( $this->context, 'attribute', $attributeStub );


		\Aimeos\MShop\Factory::setCache( true );
		$result = $this->object->save();
		\Aimeos\MShop\Factory::setCache( false );


		$item = $manager->getItem( $item->getId(), array( 'attribute' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $item->getListItems( 'attribute', 'hidden' ) ) );
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
