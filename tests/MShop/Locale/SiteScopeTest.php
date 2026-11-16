<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Locale;


class SiteScopeTest extends \PHPUnit\Framework\TestCase
{
	use SiteUserTrait;

	private $context;
	private $site;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->allowAll();

		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( \TestHelper::context() );
		$this->site = $manager->find( 'unittest' );

		$this->user( $this->site->getSiteId() );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->site );
	}


	public function testDeny()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		SiteScope::deny( $this->context );
	}


	public function testLimit()
	{
		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( \TestHelper::context() );
		$filter = $manager->filter()->add( 'locale.site.code', '==', 'unittest' );

		$this->assertSame( $filter, SiteScope::limit( $this->context, $manager, $filter ) );
		$this->assertEquals( ['unittest'], $manager->search( $filter )->getCode()->values()->all() );
	}


	public function testLimitOutside()
	{
		// the parent site of the user's site is outside the site tree of the user
		$this->user( $this->site->getSiteId() . '99.' );

		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( \TestHelper::context() );
		$filter = $manager->filter()->add( 'locale.site.code', '==', 'unittest' );

		$this->assertCount( 0, $manager->search( SiteScope::limit( $this->context, $manager, $filter ) ) );
	}


	public function testLimitOther()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'locale/language' );
		$filter = $manager->filter();

		$this->assertEquals( $manager->filter(), SiteScope::limit( $this->context, $manager, $filter ) );
	}


	public function testLimitSuper()
	{
		$this->user( '' );
		$this->super();

		$manager = new \Aimeos\MShop\Locale\Manager\Site\Standard( \TestHelper::context() );
		$filter = $manager->filter()->add( 'locale.site.code', '==', 'unittest' );

		$this->assertCount( 1, $manager->search( SiteScope::limit( $this->context, $manager, $filter ) ) );
	}


	public function testRoot()
	{
		$this->assertEquals( $this->site->getSiteId(), SiteScope::root( $this->context ) );
	}


	public function testRootNoPolicy()
	{
		$this->context->setPolicy( null );
		$this->assertNull( SiteScope::root( $this->context ) );
	}


	public function testRootNoUserSite()
	{
		$this->user( '' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		SiteScope::root( $this->context );
	}


	public function testRootSuper()
	{
		$this->user( '' );
		$this->super();

		$this->assertNull( SiteScope::root( $this->context ) );
	}


	public function testVisible()
	{
		$this->assertTrue( SiteScope::visible( $this->context, $this->site ) );
	}


	public function testVisibleOutside()
	{
		$this->user( $this->site->getSiteId() . '99.' );
		$this->assertFalse( SiteScope::visible( $this->context, $this->site ) );
	}


	public function testVisibleOther()
	{
		$this->user( '' );

		$item = \Aimeos\MShop::create( \TestHelper::context(), 'locale/language' )->create();
		$this->assertTrue( SiteScope::visible( $this->context, $item ) );
	}


	public function testVisibleSuper()
	{
		$this->user( '' );
		$this->super();

		$this->assertTrue( SiteScope::visible( $this->context, $this->site ) );
	}


	public function testWithin()
	{
		$this->assertTrue( SiteScope::within( '1.', '1.' ) );
		$this->assertTrue( SiteScope::within( '1.', '1.2.' ) );
		$this->assertFalse( SiteScope::within( '1.', '11.' ) );
		$this->assertFalse( SiteScope::within( '1.', null ) );
	}
}
