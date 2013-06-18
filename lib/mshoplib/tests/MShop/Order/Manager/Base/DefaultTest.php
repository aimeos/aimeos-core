<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Order_Manager_Default.
 */
class MShop_Order_Manager_Base_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Order_Manager_Base_Default
	 * @access protected
	 */
	private $_object;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Manager_Default_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_context = TestHelper::getContext();
		$this->_object = new MShop_Order_Manager_Base_Default($this->_context);
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}

	public function testCreateItem()
	{
		$this->assertInstanceOf('MShop_Order_Item_Base_Interface', $this->_object->createItem());
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions($search->compare('==', 'order.base.shipping', '1.50'));
		$results = $this->_object->searchItems($search);

		if ( ($expected = reset($results)) === false ) {
			throw new MShop_Order_Exception('No order base item found');
		}

		$this->assertEquals($expected, $this->_object->getItem($expected->getId()));
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.shipping', '1.50' ),
			$search->compare( '==', 'order.base.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems($search);

		if ( ( $item = reset($results) ) === false ) {
			throw new Exception('No order base item found.');
		}

		$item->setId(null);
		$item->setComment( 'Unittest1' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );
		$itemPrice = $item->getPrice();
		$itemSavedPrice = $item->getPrice();

		$itemExp = clone $itemSaved;
		$itemExp->setComment( 'Unittest2' );
		$itemExp->setCustomerId( 'unittest2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );
		$itemExpPrice = $itemExp->getPrice();
		$itemUpdPrice = $itemUpd->getPrice();


		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->getLocale()->getLanguageId(), $itemSaved->getLocale()->getLanguageId() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );
		$this->assertEquals( $itemPrice->getValue(), $itemSavedPrice->getValue() );
		$this->assertEquals( $itemPrice->getShipping(), $itemSavedPrice->getShipping() );
		$this->assertEquals( $itemPrice->getRebate(), $itemSavedPrice->getRebate() );
		$this->assertEquals( $itemPrice->getCurrencyId(), $itemSavedPrice->getCurrencyId() );
		$this->assertEquals( $item->getProducts(), $itemSaved->getProducts() );
		$this->assertEquals( $item->getAddresses(), $itemSaved->getAddresses() );
		$this->assertEquals( $item->getCoupons(), $itemSaved->getCoupons() );
		$this->assertEquals( $item->getServices(), $itemSaved->getServices() );
		$this->assertEquals( $item->getComment(), $itemSaved->getComment() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified());

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->getLocale()->getLanguageId(), $itemUpd->getLocale()->getLanguageId() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );
		$this->assertEquals( $itemExpPrice->getValue(), $itemUpdPrice->getValue() );
		$this->assertEquals( $itemExpPrice->getShipping(), $itemUpdPrice->getShipping() );
		$this->assertEquals( $itemExpPrice->getRebate(), $itemUpdPrice->getRebate() );
		$this->assertEquals( $itemExpPrice->getCurrencyId(), $itemUpdPrice->getCurrencyId() );
		$this->assertEquals( $itemExp->getProducts(), $itemUpd->getProducts() );
		$this->assertEquals( $itemExp->getAddresses(), $itemUpd->getAddresses() );
		$this->assertEquals( $itemExp->getCoupons(), $itemUpd->getCoupons() );
		$this->assertEquals( $itemExp->getServices(), $itemUpd->getServices() );
		$this->assertEquals( $itemExp->getComment(), $itemUpd->getComment() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf('MW_Common_Criteria_Interface', $this->_object->createSearch());
	}


	public function testSearchItems()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'order.base.id', null );
		$expr[] = $search->compare( '==', 'order.base.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '>=', 'order.base.customerid', '' );
		$expr[] = $search->compare( '==', 'order.base.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'order.base.price', '53.50' );
		$expr[] = $search->compare( '==', 'order.base.shipping', '1.50' );
		$expr[] = $search->compare( '==', 'order.base.rebate', '14.50' );
		$expr[] = $search->compare( '~=', 'order.base.comment', 'This is a comment' );
		$expr[] = $search->compare( '>=', 'order.base.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'order.base.address.id', null );
		$expr[] = $search->compare( '==', 'order.base.address.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.address.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.address.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.address.company', '' );
		$expr[] = $search->compare( '==', 'order.base.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'order.base.address.title', '' );
		$expr[] = $search->compare( '==', 'order.base.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'order.base.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'order.base.address.address1', 'Durchschnitt' );
		$expr[] = $search->compare( '==', 'order.base.address.address2', '1' );
		$expr[] = $search->compare( '==', 'order.base.address.address3', '' );
		$expr[] = $search->compare( '==', 'order.base.address.postal', '20146' );
		$expr[] = $search->compare( '==', 'order.base.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.base.address.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.base.address.countryid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'order.base.address.email', 'eshop@metaways.de' );
		$expr[] = $search->compare( '==', 'order.base.address.telefax', '055544332213' );
		$expr[] = $search->compare( '==', 'order.base.address.website', 'www.metaways.net' );
		$expr[] = $search->compare( '>=', 'order.base.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.address.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'order.base.product.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.product.orderproductid', null );
		$expr[] = $search->compare( '>=', 'order.base.product.type', '' );
		$expr[] = $search->compare( '!=', 'order.base.product.productid', null );
		$expr[] = $search->compare( '==', 'order.base.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.base.product.suppliercode', 'unitsupplier' );
		$expr[] = $search->compare( '==', 'order.base.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.base.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.base.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.base.product.shipping', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.taxrate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.base.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.status', 1 );
		$expr[] = $search->compare( '>=', 'order.base.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.productid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'order.base.service.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'OGONE' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'ogone' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.shipping', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.taxrate', '0.00' );
		$expr[] = $search->compare( '>=', 'order.base.service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'order.base.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.serviceid', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '>=', 'order.base.customerid', '' ),
			$search->compare( '==', 'order.base.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$total = 0;
		$items = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 4, $total );

		foreach($items as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address', 'Default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('product') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('product', 'Default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('service') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('service', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('address', 'unknown');
	}


	public function testLoadStore()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '==', 'order.base.rebate', 14.50 );
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '==', 'order.base.price', 53.50 );
		$expr[] = $search->compare( '==', 'order.base.editor', $this->_editor );
		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems($search);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search );

		if ( ( $item = reset($results) ) === false ) {
			throw new Exception('No order found');
		}

		$basket = $this->_object->load( $item->getId(), true );
		$this->_object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->_object->load( $newBasketId, true );
		$this->_object->deleteItem( $newBasketId );


		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		// because of FreeShipping plugin price is not 6.50
		$this->assertEquals( 1.50, $basket->getPrice()->getShipping() );

		$pos = 1;
		$products = $basket->getProducts();
		$this->assertEquals( 4, count( $products ) );

		foreach ( $products as $product )
		{
			$this->assertEquals( 2, count( $product->getAttributes() ) );
			$this->assertEquals( $pos++, $product->getPosition() );
		}

		$this->assertEquals( 2, count( $basket->getAddresses() ) );

		$services = $basket->getServices();
		$this->assertEquals( 2, count( $services ) );

		$attributes = array();
		foreach ( $services as $service ) {
			$attributes[ $service->getCode() ] = $service->getAttributes();
		}

		$this->assertEquals( 9, count( $attributes['OGONE'] ) );
		$this->assertEquals( 0, count( $attributes['73'] ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $newBasketId );
	}

	public function testLoadStoreBundles()
	{
		$search = $this->_object->createSearch();
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '==', 'order.base.price', 4800.00 );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search );

		if ( ( $item = reset( $results ) ) == false ) {
			throw new Exception('No order found');
		}

		$basket = $this->_object->load( $item->getId(), true );
		$this->_object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->_object->load( $newBasketId, true );
		$this->_object->deleteItem( $newBasketId );

		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		$pos = 1;
		$products = $basket->getProducts();

		$this->assertEquals( 2, count( $products ) );
		foreach ( $products as $product )
		{
			$this->assertEquals( 2, count( $product->getProducts() ) );
			$this->assertEquals( $pos, $product->getPosition() );
			$pos += 3; // two sub-products in between
		}

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $newBasketId );
	}

	public function testLoad()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare('==', 'order.base.price', 53.50 ),
			$search->compare('==', 'order.base.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if ( ( $item = reset($results) ) === false ) {
			throw new Exception('No order found');
		}

		$order = $this->_object->load( $item->getId() );
	}


	public function testLoadFresh()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare('==', 'order.base.price', 53.50 ),
			$search->compare('==', 'order.base.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems($search);

		if ( ( $item = reset($results) ) === false ) {
			throw new Exception('No order found');
		}

		$order = $this->_object->load( $item->getId(), true );

		$this->assertEquals( 0, count( $order->getCoupons() ) );
	}


	public function testGetSetSession()
	{
		$manager = new MShop_Common_Manager_Decorator_Sitecheck( TestHelper::getContext(), $this->_object );

		$order = $manager->createItem();
		$order->setComment( 'test comment' );

		$manager->setSession( $order, 'test' );
		$session = $manager->getSession( 'test' );

		$this->assertInstanceof( 'MShop_Order_Item_Base_Interface', $session );
		$this->assertEquals( 'test comment', $order->getComment() );
		$this->assertEquals( $order, $session );
	}


	public function testGetSetSessionLock()
	{
		$manager = new MShop_Common_Manager_Decorator_Sitecheck( TestHelper::getContext(), $this->_object );

		$lock = $manager->getSessionLock( 'test' );
		$this->assertEquals( MShop_Order_Manager_Base_Abstract::LOCK_DISABLE, $lock );

		$manager->setSessionLock( MShop_Order_Manager_Base_Abstract::LOCK_ENABLE, 'test' );

		$lock = $manager->getSessionLock( 'test' );
		$this->assertEquals( MShop_Order_Manager_Base_Abstract::LOCK_ENABLE, $lock );
	}
}
