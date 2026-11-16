<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Locale\Manager\Site\Decorator;


class ScopeTest extends \PHPUnit\Framework\TestCase
{
	use \Aimeos\MShop\Locale\SiteUserTrait;

	private $context;
	private $manager;
	private $object;
	private $other;
	private $site;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->allowAll();

		// records the write calls instead of executing them to keep the site fixtures untouched
		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( $this->context );
		$this->manager = new class( $manager, $this->context ) extends \Aimeos\MShop\Common\Manager\Decorator\Base {
			public array $calls = [];

			public function delete( $items ) : static {
				$this->calls[] = 'delete'; return $this;
			}

			public function insert( \Aimeos\MShop\Locale\Item\Site\Iface $item, ?string $parentId = null, ?string $refId = null ) : \Aimeos\MShop\Locale\Item\Site\Iface {
				$this->calls[] = 'insert'; return $item;
			}

			public function move( string $id, ?string $oldParentId = null, ?string $newParentId = null, ?string $refId = null ) : static {
				$this->calls[] = 'move'; return $this;
			}

			public function save( $items, bool $fetch = true ) {
				$this->calls[] = 'save'; return $items;
			}
		};

		$this->object = new \Aimeos\MShop\Locale\Manager\Site\Decorator\Scope( $this->manager, $this->context );
		$this->object->setObject( $this->object );

		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( \TestHelper::context() );
		$this->site = $manager->find( 'unittest' );
		$this->other = $manager->find( 'default' );

		$this->user( $this->site->getSiteId() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->manager, $this->context, $this->site, $this->other );
	}


	public function testDelete()
	{
		$this->assertSame( $this->object, $this->object->delete( [$this->site, $this->site->getId()] ) );
		$this->assertEquals( ['delete'], $this->manager->calls );
	}


	public function testDeleteDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->delete( [$this->site->getId(), $this->other->getId()] );
	}


	public function testDeleteUnknown()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->delete( [-1] );
	}


	public function testInsert()
	{
		$item = $this->object->create();

		$this->assertSame( $item, $this->object->insert( $item, $this->site->getId() ) );
		$this->assertEquals( ['insert'], $this->manager->calls );
	}


	public function testInsertDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->insert( $this->object->create(), $this->other->getId() );
	}


	public function testInsertRoot()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->insert( $this->object->create() );
	}


	public function testMove()
	{
		$this->assertSame( $this->object, $this->object->move( $this->site->getId(), null, $this->site->getId() ) );
		$this->assertEquals( ['move'], $this->manager->calls );
	}


	public function testMoveDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->move( $this->site->getId(), null, $this->other->getId() );
	}


	public function testMoveRoot()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->move( $this->site->getId() );
	}


	public function testSave()
	{
		$this->assertSame( $this->site, $this->object->save( $this->site ) );
		$this->assertEquals( ['save'], $this->manager->calls );
	}


	public function testSaveChangedSiteid()
	{
		// the stored site is checked, not the site ID of the passed item
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( [( clone $this->other )->setSiteId( $this->site->getSiteId() )] );
	}


	public function testSaveChangedSiteidOutside()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( ( clone $this->site )->setSiteId( $this->other->getSiteId() ) );
	}


	public function testSaveDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( [$this->site, $this->other] );
	}


	public function testSaveNoUserSite()
	{
		$this->user( '' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->object->save( $this->site );
	}


	public function testSaveNoPolicy()
	{
		$this->context->setPolicy( null );

		$this->assertSame( $this->other, $this->object->save( $this->other ) );
		$this->assertEquals( ['save'], $this->manager->calls );
	}


	public function testSaveSuper()
	{
		$this->user( '' );
		$this->super();

		$this->assertSame( $this->other, $this->object->save( $this->other ) );
		$this->assertEquals( ['save'], $this->manager->calls );
	}
}
