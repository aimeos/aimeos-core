<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Product\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'product.id' => 1,
			'product.siteid' => '1.33.99.',
			'product.type' => 'test',
			'product.status' => 1,
			'product.code' => 'TEST',
			'product.dataset' => 'Shirts',
			'product.url' => 'test_product',
			'product.label' => 'testproduct',
			'product.config' => array( 'css-class' => 'test' ),
			'product.datestart' => null,
			'product.dateend' => null,
			'product.scale' => '',
			'product.ctime' => '2011-01-19 17:04:32',
			'product.mtime' => '2011-01-19 18:04:32',
			'product.editor' => 'unitTestUser',
			'product.target' => 'testtarget',
			'product.rating' => '4.80',
			'product.ratings' => 5,
			'product.instock' => 1,
			'additional' => 'value',
		);

		$propItems = array(
			2 => new \Aimeos\MShop\Common\Item\Property\Standard( 'product.property.', array(
				'product.property.id' => 2,
				'product.property.parentid' => 1,
				'product.property.type' => 'proptest',
				'product.property.languageid' => 'de',
				'.languageid' => 'de',
			) ),
			3 => new \Aimeos\MShop\Common\Item\Property\Standard( 'product.property.', array(
				'product.property.id' => 3,
				'product.property.parentid' => 1,
				'product.property.type' => 'proptype',
				'product.property.languageid' => 'de',
				'.languageid' => 'fr',
			) ),
		);

		$this->object = new \Aimeos\MShop\Product\Item\Standard( $this->values, [], [], $propItems );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->values );
	}


	public function testDynamicMethods()
	{
		\Aimeos\MShop\Product\Item\Standard::macro( 'test', function( $name ) {
			return $this->bdata[$name];
		} );

		$this->assertInstanceOf( '\Closure', \Aimeos\MShop\Product\Item\Standard::macro( 'test' ) );

		$object = new \Aimeos\MShop\Product\Item\Standard( $this->values );
		$this->assertEquals( 'TEST', $object->test( 'product.code' ) );

		$this->expectException( \BadMethodCallException::class );
		$object->invalid();
	}


	public function testDynamicBaseMethods()
	{
		\Aimeos\MShop\Common\Item\Base::macro( 'tests', function( $name ) {
			return $this->bdata[$name];
		} );

		$this->assertInstanceOf( '\Closure', \Aimeos\MShop\Product\Item\Standard::macro( 'tests' ) );

		$object = new \Aimeos\MShop\Product\Item\Standard( $this->values );
		$this->assertEquals( 'TEST', $object->tests( 'product.code' ) );

		$this->expectException( \BadMethodCallException::class );
		$object->invalid();
	}


	public function testArrayMethods()
	{
		$this->assertFalse( isset( $this->object['test'] ) );
		$this->assertEquals( null, $this->object['test'] );

		$this->object['test'] = 'value';

		$this->assertTrue( isset( $this->object['test'] ) );
		$this->assertEquals( 'value', $this->object['test'] );

		$this->expectException( \LogicException::class );
		unset( $this->object['test'] );
	}


	public function testMagicMethods()
	{
		$this->assertFalse( isset( $this->object->test ) );
		$this->assertEquals( null, $this->object->test );

		$this->object->test = 'value';

		$this->assertTrue( isset( $this->object->test ) );
		$this->assertEquals( 'value', $this->object->test );

		$this->assertEquals( '1', (string) $this->object );
	}


	public function testGetSet()
	{
		$this->assertEquals( false, $this->object->get( 'test', false ) );

		$return = $this->object->set( 'test', 'value' );

		$this->assertEquals( 'value', $this->object->get( 'test', false ) );
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
	}


	public function testAssign()
	{
		$this->assertEquals( false, $this->object->get( 'test', false ) );

		$return = $this->object->assign( ['test' => 'value'] );

		$this->assertEquals( 'value', $this->object->get( 'test', false ) );
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
	}


	public function testGetId()
	{
		$this->assertEquals( '1', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '1', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '1.33.99.', $this->object->getSiteId() );
	}


	public function testGetSitePath()
	{
		$this->assertEquals( ['1.', '1.33.', '1.33.99.'], $this->object->getSitePath() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'test', $this->object->getType() );
	}


	public function testSetType()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setType( 'default' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'default', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'TEST', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setCode( 'NEU' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'NEU', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDataset()
	{
		$this->assertEquals( 'Shirts', $this->object->getDataset() );
	}


	public function testSetDataset()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setDataset( 'Skirts' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'Skirts', $this->object->getDataset() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetScale()
	{
		$this->assertEquals( 1, $this->object->getScale() );
	}


	public function testSetScale()
	{
		$return = $this->object->setScale( 0.25 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 0.25, $this->object->getScale() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetScaleInvalid()
	{
		$return = $this->object->setScale( -1 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 1, $this->object->getScale() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 8 );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 8, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testproduct', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'editproduct' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'editproduct', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'test_product', $this->object->getUrl() );
	}


	public function testSetUrl()
	{
		$return = $this->object->setUrl( 'edit_product' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'edit_product', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'css-class' => 'test' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( 'test', $this->object->getConfigValue( 'css-class' ) );
	}


	public function testSetConfig()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setConfig( array( 'key' => 'value' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( array( 'key' => 'value' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetConfigValue()
	{
		$result = $this->object->setConfigValue( 'path/to/value', 'test' );
		$expected = ['path' => ['to' => ['value' => 'test']], 'css-class' => 'test'];

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $result );
		$this->assertEquals( $expected, $this->object->getConfig() );

		$result = $this->object->setConfigValue( 'path/to/value2', 'test2' );
		$expected = ['path' => ['to' => ['value' => 'test', 'value2' => 'test2']], 'css-class' => 'test'];

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $result );
		$this->assertEquals( $expected, $this->object->getConfig() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'testtarget', $this->object->getTarget() );
	}


	public function testSetTarget()
	{
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setTarget( 'ttarget' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( 'ttarget', $this->object->getTarget() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( null, $this->object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( null, $this->object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:00', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-19 18:04:32', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-19 17:04:32', $this->object->getTimeCreated() );
	}


	public function testSetTimeCreated()
	{
		$return = $this->object->setTimeCreated( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getTimeCreated() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRating()
	{
		$this->assertEquals( '4.80', $this->object->getRating() );
	}


	public function testGetRatings()
	{
		$this->assertEquals( 5, $this->object->getRatings() );
	}


	public function testInStock()
	{
		$this->assertEquals( 1, $this->object->inStock() );
		$this->assertEquals( 0, $this->object->inStock( 0 ) );
		$this->assertEquals( 0, $this->object->inStock() );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnTime()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setDateStart( date( 'Y-m-d H:i:s', time() + 600 ) );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setDateEnd( date( 'Y-m-d H:i:s', time() - 600 ) );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableEvent()
	{
		$this->object->setType( 'event' );
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setDateStart( date( 'Y-m-d H:i:s', time() + 600 ) );
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setDateEnd( date( 'Y-m-d H:i:s', time() - 600 ) );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testIsModifiedTrue()
	{
		$this->object->setLabel( 'reeditProduct' );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'product', $this->object->getResourceType() );
	}


	public function testGetPropertyItems()
	{
		$propItems = $this->object->getPropertyItems();

		$this->assertEquals( 1, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testGetCatalogItems()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( ['.catalog' => []] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getCatalogItems() );
		$this->assertEquals( [], $object->getCatalogItems()->toArray() );
	}


	public function testGetSupplierItems()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( ['.supplier' => []] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getSupplierItems() );
		$this->assertEquals( [], $object->getSupplierItems()->toArray() );
	}


	public function testGetStockItems()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( ['.stock' => []] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getStockItems() );
		$this->assertEquals( [], $object->getStockItems()->toArray() );
	}


	public function testGetStockItemsType()
	{
		$stock = new \Aimeos\MShop\Stock\Item\Standard();
		$stocks = [123 => ( clone $stock )->setType( 'something' ), 456 => ( clone $stock )->setType( 'default' )];
		$object = new \Aimeos\MShop\Product\Item\Standard( ['.stock' => $stocks] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getStockItems( 'default' ) );
		$this->assertEquals( 'default', $object->getStockItems( 'default' )->getType()->first() );
		$this->assertCount( 1, $object->getStockItems( 'default' ) );
	}


	public function testGetPropertyItemsAll()
	{
		$propItems = $this->object->getPropertyItems( null, false );

		$this->assertEquals( 2, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testGetPropertyItemsType()
	{
		$propItems = $this->object->getPropertyItems( 'proptest' );

		$this->assertEquals( 1, count( $propItems ) );

		foreach( $propItems as $propItem ) {
			$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $propItem );
		}
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Product\Item\Standard();

		$list = $entries = array(
			'product.id' => 1,
			'product.type' => 'test',
			'product.label' => 'test item',
			'product.url' => 'test_item',
			'product.code' => 'test',
			'product.dataset' => 'Shirts',
			'product.datestart' => '2000-01-01 00:00:00',
			'product.dateend' => '2001-01-01 00:00:00',
			'product.config' => array( 'key' => 'value' ),
			'product.status' => 0,
			'product.scale' => '0.5',
			'product.target' => 'ttarget',
			'product.instock' => 1,
			'additional' => 'value',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( ['additional' => 'value'], $entries );
		$this->assertEquals( $list['product.id'], $item->getId() );
		$this->assertEquals( $list['product.url'], $item->getUrl() );
		$this->assertEquals( $list['product.type'], $item->getType() );
		$this->assertEquals( $list['product.code'], $item->getCode() );
		$this->assertEquals( $list['product.label'], $item->getLabel() );
		$this->assertEquals( $list['product.dataset'], $item->getDataset() );
		$this->assertEquals( $list['product.datestart'], $item->getDateStart() );
		$this->assertEquals( $list['product.dateend'], $item->getDateEnd() );
		$this->assertEquals( $list['product.config'], $item->getConfig() );
		$this->assertEquals( $list['product.status'], $item->getStatus() );
		$this->assertEquals( $list['product.target'], $item->getTarget() );
		$this->assertEquals( $list['product.scale'], $item->getScale() );
		$this->assertEquals( $list['product.instock'], $item->inStock() );
		$this->assertEquals( $list['additional'], $item->additional );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.siteid'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['product.code'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.type'] );
		$this->assertEquals( $this->object->getDataset(), $arrayObject['product.dataset'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['product.label'] );
		$this->assertEquals( $this->object->getUrl(), $arrayObject['product.url'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['product.status'] );
		$this->assertEquals( $this->object->getDateStart(), $arrayObject['product.datestart'] );
		$this->assertEquals( $this->object->getDateEnd(), $arrayObject['product.dateend'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['product.config'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.editor'] );
		$this->assertEquals( $this->object->getTarget(), $arrayObject['product.target'] );
		$this->assertEquals( $this->object->getScale(), $arrayObject['product.scale'] );
		$this->assertEquals( $this->object->getRating(), $arrayObject['product.rating'] );
		$this->assertEquals( $this->object->getRatings(), $arrayObject['product.ratings'] );
		$this->assertEquals( $this->object->inStock(), $arrayObject['product.instock'] );
	}
}
