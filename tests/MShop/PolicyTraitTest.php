<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop;


class PolicyTraitTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\Base\View\Standard();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $view, ['editor'] ) );

		$this->context = \TestHelper::context()->setView( $view )->setPolicy( null );
		// runtime values can't be read by the keys of their sub-arrays
		$this->context->config()->set( 'test/resource', ['order' => ['groups' => []], 'product' => ['groups' => []], 'service' => ['groups' => []]] );

		foreach( ['order', 'product', 'service'] as $resource ) {
			$this->context->config()->set( 'test/resource/' . $resource, ['groups' => []] );
		}

		$this->object = new class {
			use \Aimeos\MShop\PolicyTrait;

			public static function policy() : \Aimeos\MShop\PolicyIface
			{
				return new class implements \Aimeos\MShop\PolicyIface {
					public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
						return ['order' => true, 'order/service' => false, 'product' => true, 'service' => false][$resource] ?? null;
					}
				};
			}

			protected static function prefix() : string
			{
				return 'test/resource';
			}

			public static function context( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
			{
				return self::withPolicy( $context );
			}
		};
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testAllowed()
	{
		$this->assertTrue( $this->object::allowed( $this->context, 'order', 'get' ) );
		$this->assertFalse( $this->object::allowed( $this->context, 'order/service', 'get' ) );
	}


	public function testAllowedUnconfigured()
	{
		// resources without rules are only accessible by super users
		$this->assertFalse( $this->object::allowed( $this->context, 'order/address', 'get' ) );
	}


	public function testAllowedNoView()
	{
		$context = new \Aimeos\MShop\Context();

		// without a view, the user groups can't be checked so access is denied
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object::allowed( $context, 'order/address', 'get' );
	}


	public function testAllowedInherit()
	{
		$this->assertTrue( $this->object::allowed( $this->context, 'order/address', 'get', inherit: true ) );
		$this->assertFalse( $this->object::allowed( $this->context, 'order/service', 'get', inherit: true ) );
		$this->assertFalse( $this->object::allowed( $this->context, 'unknown/address', 'get', inherit: true ) );
	}


	public function testAllowedContextPolicy()
	{
		$this->context->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return false;
			}
		} );

		$this->assertFalse( $this->object::allowed( $this->context, 'order', 'get' ) );
	}


	public function testAllowedFallbackResource()
	{
		$object = new class {
			use \Aimeos\MShop\PolicyTrait;

			public static function policy() : \Aimeos\MShop\PolicyIface
			{
				return new class implements \Aimeos\MShop\PolicyIface {
					public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
						return null;
					}
				};
			}

			protected static function prefix() : string
			{
				return 'test/resource';
			}

			protected static function fallback( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : bool
			{
				return $resource === 'order/address';
			}
		};

		// the fallback gets the requested resource, not the last parent checked ("order")
		$this->assertTrue( $object::allowed( $this->context, 'order/address', 'get', inherit: true ) );
	}


	public function testCheck()
	{
		$this->expectNotToPerformAssertions();
		$this->object::check( $this->context, 'order', 'get' );
	}


	public function testCheckDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->expectExceptionMessageMatches( '/^Action "get" is not allowed for "service"$/' );
		$this->object::check( $this->context, 'service', 'get' );
	}


	public function testCheckRoot()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionMessageMatches( '/^Action "get" is not allowed$/' );
		$this->object::check( $this->context, '', 'get' );
	}


	public function testDenial()
	{
		$e = $this->object::denial( $this->context, 'order/address', 'save' );

		$this->assertInstanceOf( \Aimeos\MShop\PolicyException::class, $e );
		$this->assertEquals( 'Action "save" is not allowed for "order/address"', $e->getMessage() );
		$this->assertEquals( 403, $e->getCode() );
	}


	public function testForbidden()
	{
		$previous = new \RuntimeException( 'test' );
		$e = $this->object::forbidden( 'test', $previous );

		$this->assertInstanceOf( \Aimeos\MShop\PolicyException::class, $e );
		$this->assertSame( $previous, $e->getPrevious() );
		$this->assertEquals( 403, $e->getCode() );
	}


	public function testPermitted()
	{
		$list = ['order', 'order/address', 'order/service'];

		$this->assertEquals( ['order'], $this->object::permitted( $this->context, $list, 'get' ) );
		$this->assertEquals( ['order', 'order/address'], $this->object::permitted( $this->context, $list, 'get', inherit: true ) );
	}


	public function testReadable()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$item = $manager->create()->setDomain( 'product' );

		$this->assertSame( $item, $this->object::readable( $this->context, $item ) );
		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $this->object::readable( $this->context, $manager->create() ) );
	}


	public function testReadableDenied()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );

		// denied items are reported like missing items so their IDs don't reveal existing items
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->expectExceptionMessage( 'Item with ID "1" in "price.id" not found' );
		$this->object::readable( $this->context, $manager->create()->setId( '1' )->setDomain( 'service' ) );
	}


	public function testReadableString()
	{
		// single group names as strings are accepted by the policies, so the resource is protected
		$this->context->config()->set( 'test/resource/product', ['groups' => 'editor'] );
		$item = \Aimeos\MShop::create( $this->context, 'price' )->create()->setDomain( 'product' );

		$this->assertSame( $item, $this->object::readable( $this->context, $item ) );
	}


	public function testReadableUnknown()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );

		// owning domains which aren't resources of the entry point are denied
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->object::readable( $this->context, $manager->create()->setDomain( 'unknown' ) );
	}


	public function testReadableEmpty()
	{
		$this->context->config()->set( 'test/resource/unknown', [] );
		$manager = \Aimeos\MShop::create( $this->context, 'price' );

		// empty lists are lists of user groups, not resources
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->object::readable( $this->context, $manager->create()->setDomain( 'unknown' ) );
	}


	public function testReadableSubDomain()
	{
		// sub-domains like the ones of property types are owned by their top-level domain
		$item = \Aimeos\MShop::create( $this->context, 'price' )->create()->setDomain( 'product/property' );

		$this->assertSame( $item, $this->object::readable( $this->context, $item ) );
	}


	public function testReadableSubDomainDenied()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->object::readable( $this->context, $manager->create()->setDomain( 'service/property' ) );
	}


	public function testReadableLeadingSlash()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );

		// malformed domains aren't mapped to top-level domains
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->object::readable( $this->context, $manager->create()->setDomain( '/product' ) );
	}


	public function testReadableNoOwner()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->assertSame( $item, $this->object::readable( $this->context, $item ) );
	}


	public function testRestrict()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$this->object::restrict( $this->context, $manager, $filter = $manager->filter() );

		$expected = $manager->filter();
		$expected->add( $expected->or( [
			$expected->compare( '==', 'price.domain', ['', 'order', 'product'] ),
			$expected->compare( '=~', 'price.domain', ['order/', 'product/'] ),
		] ) );

		$this->assertEquals( $expected->getConditions(), $filter->getConditions() );
	}


	public function testRestrictGroups()
	{
		// lists of user groups like the ones of the root resource aren't resources
		$this->context->config()->set( 'test/resource/groups', ['editor'] );

		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$this->object::restrict( $this->context, $manager, $filter = $manager->filter() );

		$expected = $manager->filter();
		$expected->add( $expected->or( [
			$expected->compare( '==', 'price.domain', ['', 'order', 'product'] ),
			$expected->compare( '=~', 'price.domain', ['order/', 'product/'] ),
		] ) );

		$this->assertEquals( $expected->getConditions(), $filter->getConditions() );
	}


	public function testRestrictNone()
	{
		$this->context->config()->set( 'test/resource', ['service' => ['groups' => []]] );

		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$this->object::restrict( $this->context, $manager, $filter = $manager->filter() );

		// no prefix condition if the user isn't allowed to retrieve items of any domain
		$expected = $manager->filter();
		$expected->add( $expected->or( [$expected->compare( '==', 'price.domain', [''] )] ) );

		$this->assertEquals( $expected->getConditions(), $filter->getConditions() );
	}


	public function testRestrictNoOwner()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$this->object::restrict( $this->context, $manager, $filter = $manager->filter() );

		$this->assertEquals( $manager->filter()->getConditions(), $filter->getConditions() );
	}


	public function testWithPolicy()
	{
		$context = $this->object::context( $this->context );

		// the passed context may be shared, so only the copy contains the policy
		$this->assertNotSame( $this->context, $context );
		$this->assertNull( $this->context->policy() );
		$this->assertInstanceOf( \Aimeos\MShop\PolicyIface::class, $context->policy() );
	}


	public function testWithPolicyExisting()
	{
		$this->context->setPolicy( $this->object::policy() );

		$this->assertSame( $this->context, $this->object::context( $this->context ) );
	}
}
