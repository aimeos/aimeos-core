<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\MShop\Group\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object = null;
	private $context = null;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->editor = $this->context->editor();

		$this->object = new \Aimeos\MShop\Group\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreate()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Group\Item\Iface::class, $item );
	}


	public function testFind()
	{
		$item = $this->object->find( 'unitgroup' );

		$this->assertEquals( 'unitgroup', $item->getCode() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'group.label', 'Unitgroup' ) );

		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No group item with label "Unitgroup" found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDelete()
	{
		$item = $this->object->create();
		$item->setCode( 'unittest-group' );
		$item->setLabel( 'unittest group' );

		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unittest 2. group' );

		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFilter()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testSearchItem()
	{
		$total = 0;
		$search = $this->object->filter()->add( 'group.code', '~=', 'unitgroup' )->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );
	}


	public function testSaveSuper()
	{
		$this->access( true );

		$item = $this->object->save( $this->object->create()->setCode( 'super' )->setLabel( 'Super' ) );
		$this->object->delete( $item );

		$this->assertNotNull( $item->getId() );
	}


	public function testSaveSuperDenied()
	{
		$this->access( false );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( $this->object->create()->setCode( 'super ' )->setLabel( 'Super' ) );
	}


	public function testSaveRenameToSuperDenied()
	{
		$this->access( false );
		$item = $this->object->find( 'unitgroup' )->setCode( 'super' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( $item );
	}


	public function testSaveRenameFromSuperDenied()
	{
		$this->access( true );
		$item = $this->object->save( $this->object->create()->setCode( 'super' )->setLabel( 'Super' ) );

		try
		{
			$this->access( false );
			$this->object->save( $item->setCode( 'nosuper' ) );
			$this->fail( 'Renaming the "super" group must not be possible' );
		}
		catch( \Aimeos\MShop\PolicyException $e )
		{
			$this->assertEquals( 403, $e->getCode() );
		}
		finally
		{
			$this->access( true );
			$this->object->delete( $item->getId() );
		}
	}


	public function testDeleteSuperDenied()
	{
		$this->access( true );
		$item = $this->object->save( $this->object->create()->setCode( 'super' )->setLabel( 'Super' ) );

		try
		{
			$this->access( false );
			$this->object->delete( $item->getId() );
			$this->fail( 'Deleting the "super" group must not be possible' );
		}
		catch( \Aimeos\MShop\PolicyException $e )
		{
			$this->assertEquals( 403, $e->getCode() );
		}
		finally
		{
			$this->access( true );
			$this->object->delete( $item->getId() );
		}
	}


	protected function access( bool $super ) : void
	{
		$view = new \Aimeos\Base\View\Standard();

		$helper = $super
			? new \Aimeos\Base\View\Helper\Access\All( $view )
			: new \Aimeos\Base\View\Helper\Access\Standard( $view, ['admin'] );

		$this->context->setView( $view->addHelper( 'access', $helper ) );
	}
}
