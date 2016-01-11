<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JQAdm\Product\Text;


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

		$this->object = new \Aimeos\Admin\JQAdm\Product\Text\Standard( $this->context, $templatePaths );
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

		$this->assertContains( 'Texts', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'ABCD', array( 'text' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="Unterproduct 1"', $result );
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

		$this->view->item = $manager->findItem( 'ABCD', array( 'text' ) );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'value="Unterproduct 1"', $result );
	}


	public function testSave()
	{
		$typeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'text/type' );
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'CNC' );
		$item->setCode( 'jqadm-test-save' );
		$item->setId( null );

		$manager->saveItem( $item );


		$param = array(
			'text' => array(
				'langid' => array( 'de' ),
				'name' => array( 'listid' => '', 'content' => 'test name' ),
				'short' => array( 'listid' => '', 'content' => 'short desc' ),
				'long' => array( 'listid' => '', 'content' => 'long desc' ),
				'url' => array( 'listid' => '', 'content' => 'url segment' ),
				'meta-keyword' => array( 'listid' => '', 'content' => 'meta keywords' ),
				'meta-description' => array( 'listid' => '', 'content' => 'meta desc' ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'text' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 6, count( $item->getListItems() ) );

		foreach( $item->getListItems( 'text' ) as $listItem )
		{
			$this->assertEquals( 'text', $listItem->getDomain() );
			$this->assertEquals( 'default', $listItem->getType() );

			$refItem = $listItem->getRefItem();
			$this->assertEquals( 'de', $refItem->getLanguageId() );
		}
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}
}
