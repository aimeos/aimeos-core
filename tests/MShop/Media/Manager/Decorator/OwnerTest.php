<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Media\Manager\Decorator;


class OwnerTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $mediaId;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->context->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return ['product' => true, 'service' => false][$resource] ?? null;
			}
		} );

		$this->object = new \Aimeos\MShop\Media\Manager\Decorator\Owner( new \Aimeos\MShop\Media\Manager\Standard( $this->context ), $this->context );
		$this->object->setObject( $this->object );
	}


	protected function tearDown() : void
	{
		if( $this->mediaId ) {
			\Aimeos\MShop::create( \TestHelper::context(), 'media' )->delete( $this->mediaId );
		}

		unset( $this->object, $this->context );
	}


	public function testCopy()
	{
		$item = $this->item( 'product' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $this->object->copy( $item ) );
	}


	public function testCopyDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->copy( $this->item( 'service' ) );
	}


	public function testScale()
	{
		$item = $this->item( 'product' );

		$this->assertSame( $item, $this->object->scale( $item ) );
	}


	public function testScaleDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->scale( $this->item( 'service' ) );
	}


	public function testUpload()
	{
		$item = $this->item( 'product' );

		$this->assertSame( $item, $this->object->upload( $item, null ) );
	}


	public function testUploadDenied()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->expectExceptionMessageMatches( '/^Permission "save" for "service" is required to change "media" items$/' );
		$this->object->upload( $this->item( 'service' ), null );
	}


	public function testUploadDeniedNamed()
	{
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->upload( item: $this->item( 'service' ), file: null );
	}


	public function testUploadDeniedStored()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'media' );
		$item = $manager->save( $manager->create()->setDomain( 'service' )->setUrl( 'owner-test.jpg' ) );
		$this->mediaId = $item->getId();

		// files of denied domains must not be changed by passing another owning domain
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->upload( $item->setDomain( 'product' ), null );
	}


	public function testUploadUnrestricted()
	{
		$this->context->setPolicy( null );
		$item = $this->item( 'service' );

		$this->assertSame( $item, $this->object->upload( $item, null ) );
	}


	public function testSaveDenied()
	{
		// save() and delete() are inherited from the common Owner decorator
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->save( $this->item( 'service' )->setUrl( 'owner-test.jpg' ) );
	}


	public function testFactory()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'media' );

		// the media decorator replaces the common one, so the file operations are checked
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$manager->upload( $this->item( 'service' ), null );
	}


	protected function item( string $domain ) : \Aimeos\MShop\Media\Item\Iface
	{
		return \Aimeos\MShop::create( \TestHelper::context(), 'media' )->create()->setDomain( $domain );
	}
}
