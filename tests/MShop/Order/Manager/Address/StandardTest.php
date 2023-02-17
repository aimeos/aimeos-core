<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Manager\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object = null;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\MShop\Order\Manager\Address\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.address.editor', 'core' ) );
		$result = $this->object->aggregate( $search, 'order.address.salutation' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MS, $result );
		$this->assertEquals( 4, $result[\Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MS] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $item );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/address', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetItem()
	{
		$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY;

		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.address.type', $type ),
			$search->compare( '==', 'order.address.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order base address item found for type "%1$s".', $type ) );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}



	public function testSaveUpdateDeleteItem()
	{
		$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY;

		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.address.type', $type ),
			$search->compare( '==', 'order.address.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order base address item found for type "%1$s".', $type ) );
		}

		$this->object->delete( $item->getId() );
		$firstname = $item->getFirstname();
		$oldId = $item->getId();

		$item->setId( null );
		$item->setFirstname( 'unittestdata' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setFirstname( $firstname );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getAddressId(), $itemSaved->getAddressId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCompany(), $itemSaved->getCompany() );
		$this->assertEquals( $item->getVatID(), $itemSaved->getVatID() );
		$this->assertEquals( $item->getSalutation(), $itemSaved->getSalutation() );
		$this->assertEquals( $item->getTitle(), $itemSaved->getTitle() );
		$this->assertEquals( $item->getFirstname(), $itemSaved->getFirstname() );
		$this->assertEquals( $item->getLastname(), $itemSaved->getLastname() );
		$this->assertEquals( $item->getAddress1(), $itemSaved->getAddress1() );
		$this->assertEquals( $item->getAddress2(), $itemSaved->getAddress2() );
		$this->assertEquals( $item->getAddress3(), $itemSaved->getAddress3() );
		$this->assertEquals( $item->getPostal(), $itemSaved->getPostal() );
		$this->assertEquals( $item->getCity(), $itemSaved->getCity() );
		$this->assertEquals( $item->getState(), $itemSaved->getState() );
		$this->assertEquals( $item->getCountryId(), $itemSaved->getCountryId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getTelephone(), $itemSaved->getTelephone() );
		$this->assertEquals( $item->getEmail(), $itemSaved->getEmail() );
		$this->assertEquals( $item->getTelefax(), $itemSaved->getTelefax() );
		$this->assertEquals( $item->getWebsite(), $itemSaved->getWebsite() );
		$this->assertEquals( $item->getLongitude(), $itemSaved->getLongitude() );
		$this->assertEquals( $item->getLatitude(), $itemSaved->getLatitude() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getBirthday(), $itemSaved->getBirthday() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getAddressId(), $itemUpd->getAddressId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCompany(), $itemUpd->getCompany() );
		$this->assertEquals( $itemExp->getVatID(), $itemUpd->getVatID() );
		$this->assertEquals( $itemExp->getSalutation(), $itemUpd->getSalutation() );
		$this->assertEquals( $itemExp->getTitle(), $itemUpd->getTitle() );
		$this->assertEquals( $itemExp->getFirstname(), $itemUpd->getFirstname() );
		$this->assertEquals( $itemExp->getLastname(), $itemUpd->getLastname() );
		$this->assertEquals( $itemExp->getAddress1(), $itemUpd->getAddress1() );
		$this->assertEquals( $itemExp->getAddress2(), $itemUpd->getAddress2() );
		$this->assertEquals( $itemExp->getAddress3(), $itemUpd->getAddress3() );
		$this->assertEquals( $itemExp->getPostal(), $itemUpd->getPostal() );
		$this->assertEquals( $itemExp->getCity(), $itemUpd->getCity() );
		$this->assertEquals( $itemExp->getState(), $itemUpd->getState() );
		$this->assertEquals( $itemExp->getCountryId(), $itemUpd->getCountryId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getTelephone(), $itemUpd->getTelephone() );
		$this->assertEquals( $itemExp->getEmail(), $itemUpd->getEmail() );
		$this->assertEquals( $itemExp->getTelefax(), $itemUpd->getTelefax() );
		$this->assertEquals( $itemExp->getWebsite(), $itemUpd->getWebsite() );
		$this->assertEquals( $itemExp->getLongitude(), $itemUpd->getLongitude() );
		$this->assertEquals( $itemExp->getLatitude(), $itemUpd->getLatitude() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getBirthday(), $itemUpd->getBirthday() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $oldId );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->locale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.address.id', null );
		$expr[] = $search->compare( '==', 'order.address.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.address.parentid', null );
		$expr[] = $search->compare( '==', 'order.address.addressid', '103' );
		$expr[] = $search->compare( '==', 'order.address.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.address.company', 'Example company' );
		$expr[] = $search->compare( '==', 'order.address.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'order.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'order.address.title', '' );
		$expr[] = $search->compare( '==', 'order.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'order.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'order.address.address1', 'Durchschnitt' );
		$expr[] = $search->compare( '==', 'order.address.address2', '1' );
		$expr[] = $search->compare( '==', 'order.address.address3', '' );
		$expr[] = $search->compare( '==', 'order.address.postal', '20146' );
		$expr[] = $search->compare( '==', 'order.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.address.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.address.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'order.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'order.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'order.address.telefax', '055544332213' );
		$expr[] = $search->compare( '==', 'order.address.website', 'www.example.net' );
		$expr[] = $search->compare( '==', 'order.address.longitude', '11.0' );
		$expr[] = $search->compare( '==', 'order.address.latitude', '52.0' );
		$expr[] = $search->compare( '==', 'order.address.position', 0 );
		$expr[] = $search->compare( '==', 'order.address.birthday', '2001-01-01' );
		$expr[] = $search->compare( '>=', 'order.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.address.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		$conditions = array(
			$search->compare( '==', 'order.address.lastname', 'Unittest' ),
			$search->compare( '==', 'order.address.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 4, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
