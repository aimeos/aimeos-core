<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds order test data.
 */
class OrderAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['CustomerAddTestData', 'ProductAddTestData', 'PluginAddTestData', 'ServiceAddTestData', 'ProductAddStockTestData'];
	}


	/**
	 * Adds order test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding order test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional, 'Standard' );
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->additional, 'Standard' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderBaseManager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.sitecode', array( 'unittest', 'unit' ) ) );

		foreach( $orderBaseManager->search( $search ) as $order ) {
			$orderBaseManager->deleteItem( $order->getId() );
		}


		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'order.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for order domain', $path ) );
		}

		$this->additional->setLocale( $this->additional->getLocale()->setCurrencyId( 'EUR' ) );

		$bases = $this->addOrderBaseData( $localeManager, $orderBaseManager, $testdata );
		$bases['items'] = $this->addOrderBaseProductData( $orderBaseManager, $bases, $testdata );
		$bases['items'] = $this->addOrderBaseServiceData( $orderBaseManager, $bases, $testdata );

		//update order bases (getPrice)
		foreach( $bases['items'] as $baseItem ) {
			$orderBaseManager->saveItem( $baseItem, false );
		}

		$this->additional->setLocale( $this->additional->getLocale()->setCurrencyId( null ) );

		$this->addOrderData( $orderManager, $bases['ids'], $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the required order base data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param \Aimeos\MShop\Common\Manager\Iface $orderBaseManager Order base manager
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addOrderBaseData( \Aimeos\MShop\Common\Manager\Iface $localeManager,
		\Aimeos\MShop\Common\Manager\Iface $orderBaseManager, array $testdata )
	{
		$bases = [];
		$locale = $localeManager->createItem();
		$customerIds = $this->getCustomerIds( $testdata );
		$orderBaseAddressManager = $orderBaseManager->getSubManager( 'address', 'Standard' );

		$orderBaseManager->begin();

		foreach( $testdata['order/base'] as $key => $dataset )
		{
			$bases['items'][$key] = $orderBaseManager->createItem();
			$bases['items'][$key]->setId( null );
			$bases['items'][$key]->setComment( $dataset['comment'] );
			$bases['items'][$key]->setCustomerReference( $dataset['customerref'] );
			$bases['items'][$key]->setCustomerId( $customerIds[$dataset['customerid']] );

			$locale->setId( null );
			$locale->setSiteId( $this->additional->getLocale()->getSiteId() );
			$locale->setLanguageId( $dataset['langid'] );
			$locale->setCurrencyId( $dataset['currencyid'] );
			$bases['items'][$key]->setLocale( $locale );

			$orderBaseManager->saveItem( $bases['items'][$key] );
			$bases['ids'][$key] = $bases['items'][$key]->getId();
		}

		$this->addOrderBaseAddressData( $orderBaseAddressManager, $bases, $testdata );

		$orderBaseManager->commit();

		return $bases;
	}


	/**
	 * Adds the order address data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager
	 * @param array $testdata
	 */
	protected function addOrderBaseAddressData( \Aimeos\MShop\Common\Manager\Iface $manager,
		array $bases, array $testdata )
	{
		$orderAddr = $manager->createItem();

		foreach( $testdata['order/base/address'] as $dataset )
		{
			if( !isset( $bases['ids'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			$orderAddr->setId( null );
			$orderAddr->setBaseId( $bases['ids'][$dataset['baseid']] );
			$orderAddr->setAddressId( ( $dataset['addrid'] ?? '' ) );
			$orderAddr->setType( $dataset['type'] );
			$orderAddr->setCompany( $dataset['company'] ?? '' );
			$orderAddr->setVatID( ( $dataset['vatid'] ?? '' ) );
			$orderAddr->setSalutation( $dataset['salutation'] );
			$orderAddr->setTitle( $dataset['title'] );
			$orderAddr->setFirstname( $dataset['firstname'] );
			$orderAddr->setLastname( $dataset['lastname'] );
			$orderAddr->setAddress1( $dataset['address1'] );
			$orderAddr->setAddress2( $dataset['address2'] );
			$orderAddr->setAddress3( $dataset['address3'] );
			$orderAddr->setPostal( $dataset['postal'] );
			$orderAddr->setCity( $dataset['city'] );
			$orderAddr->setState( $dataset['state'] );
			$orderAddr->setCountryId( $dataset['countryid'] );
			$orderAddr->setTelephone( $dataset['telephone'] );
			$orderAddr->setEmail( $dataset['email'] );
			$orderAddr->setTelefax( $dataset['telefax'] );
			$orderAddr->setWebsite( $dataset['website'] );
			$orderAddr->setLanguageId( $dataset['langid'] );
			$orderAddr->setLatitude( $dataset['latitude'] );
			$orderAddr->setLongitude( $dataset['longitude'] );
			$orderAddr->setBirthday( $dataset['birthday'] ?? null );

			$manager->saveItem( $orderAddr, false );
		}
	}


	/**
	 * Adds the required order base service data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $orderBaseManager Order base manager
	 * @param array $bases Associative list of key/list pairs
	 * @param array $testdata Associative list of key/list pairs
	 * @return array Associative list of enhanced order base items
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addOrderBaseServiceData( \Aimeos\MShop\Common\Manager\Iface $orderBaseManager,
		array $bases, array $testdata )
	{
		$ordServices = [];
		$servIds = $this->getServiceIds( $testdata );
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service', 'Standard' );
		$orderBaseServiceAttrManager = $orderBaseServiceManager->getSubManager( 'attribute', 'Standard' );
		$priceManager = \Aimeos\MShop::create( $this->additional, 'price' );
		$ordServ = $orderBaseServiceManager->createItem();

		$orderBaseManager->begin();

		foreach( $testdata['order/base/service'] as $key => $dataset )
		{
			if( !isset( $bases['ids'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s" in order base serive data', $dataset['baseid'] ) );
			}

			if( !isset( $bases['items'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base Item found for "%1$s" in order base service data', $dataset['baseid'] ) );
			}

			$ordServ->setId( null );
			$ordServ->setBaseId( $bases['ids'][$dataset['baseid']] );
			$ordServ->setType( $dataset['type'] );
			$ordServ->setCode( $dataset['code'] );
			$ordServ->setName( $dataset['name'] );
			$ordServ->setMediaUrl( $dataset['mediaurl'] );

			if( isset( $dataset['servid'] ) ) {
				$ordServ->setServiceId( $servIds[$dataset['servid']] );
			}

			$priceItem = $priceManager->createItem();
			$priceItem->setCurrencyId( $dataset['currencyid'] );
			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRates( $dataset['taxrates'] );
			$ordServ->setPrice( $priceItem );

			$orderBaseServiceManager->saveItem( $ordServ );

			$ordServices[$key] = $ordServ->getId();
			$bases['items'][$dataset['baseid']]->addService( $ordServ, $dataset['type'] ); //adds Services to orderbase
		}

		$this->addOrderBaseServiceAttributeData( $orderBaseServiceAttrManager, $testdata, $ordServices );

		$orderBaseManager->commit();

		return $bases['items'];
	}


	/**
	 * Adds the required order base product data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $orderBaseManager Order Base Manager
	 * @param array $bases Associative list of key/list pairs
	 * @param array $testdata Associative list of key/list pairs
	 * @return array Enhanced list of order base items
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addOrderBaseProductData( \Aimeos\MShop\Common\Manager\Iface $orderBaseManager,
		array $bases, array $testdata )
	{
		$ordProds = [];
		$products = $this->getProductItems( $testdata );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product', 'Standard' );
		$orderBaseProductAttrManager = $orderBaseProductManager->getSubManager( 'attribute', 'Standard' );
		$priceManager = \Aimeos\MShop::create( $this->additional, 'price' );

		$orderBaseManager->begin();

		foreach( $testdata['order/base/product'] as $key => $dataset )
		{
			if( !isset( $bases['ids'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s" in order base product data', $dataset['baseid'] ) );
			}

			if( !isset( $bases['items'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base Item found for "%1$s" in order base product data', $dataset['baseid'] ) );
			}

			$ordProdItem = $orderBaseProductManager->createItem();

			$ordProdItem->setId( null );
			$ordProdItem->setBaseId( $bases['ids'][$dataset['baseid']] );
			$ordProdItem->setType( $dataset['type'] );
			$ordProdItem->setSupplierCode( $dataset['suppliercode'] );
			$ordProdItem->setProductCode( $dataset['prodcode'] );
			$ordProdItem->setName( $dataset['name'] );
			$ordProdItem->setMediaUrl( $dataset['mediaurl'] );
			$ordProdItem->setQuantity( $dataset['amount'] );
			$ordProdItem->setFlags( $dataset['flags'] );
			$ordProdItem->setStatus( $dataset['status'] );
			$ordProdItem->setPosition( $dataset['pos'] );

			if( isset( $dataset['stocktype'] ) ) {
				$ordProdItem->setStockType( $dataset['stocktype'] );
			}

			if( isset( $dataset['timeframe'] ) ) {
				$ordProdItem->setTimeFrame( $dataset['timeframe'] );
			}

			if( isset( $dataset['prodid'] ) ) {
				$ordProdItem->setProductId( $products[$dataset['prodid']]->getId() );
			}

			// product bundle related fields
			if( isset( $dataset['ordprodid'] ) ) {
				$ordProdItem->setOrderProductId( $ordProds[$dataset['ordprodid']] );
			}

			$priceItem = $priceManager->createItem();
			$priceItem->setCurrencyId( $dataset['currencyid'] );
			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRates( $dataset['taxrates'] );
			$ordProdItem->setPrice( $priceItem );

			$bases['items'][$dataset['baseid']]->addProduct( $ordProdItem, $dataset['pos'] ); //adds Products to orderbase
			$ordProds[$key] = $orderBaseProductManager->saveItem( $ordProdItem )->getId();
		}

		$this->addOrderBaseProductAttributeData( $orderBaseProductAttrManager, $testdata, $ordProds, $products );

		$orderBaseManager->commit();

		return $bases['items'];
	}


	/**
	 * Adds the order product attribute test data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager
	 * @param array $testdata
	 * @param array $ordProds
	 * @param \Aimeos\MShop\Product\Item\Iface[] $products
	 * @throws \Aimeos\MW\Setup\Exception
	 */
	protected function addOrderBaseProductAttributeData( \Aimeos\MShop\Common\Manager\Iface $manager,
		array $testdata, array $ordProds, array $products )
	{
		$attrCodes = [];
		$attributeManager = \Aimeos\MShop::create( $this->additional, 'attribute' );
		$attributes = $attributeManager->search( $attributeManager->filter() );

		foreach( $attributes as $attrItem ) {
			$attrCodes[$attrItem->getType()][] = $attrItem;
		}

		$ordProdAttr = $manager->createItem();

		foreach( $testdata['order/base/product/attr'] as $dataset )
		{
			if( !isset( $ordProds[$dataset['ordprodid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No order product ID found for "%1$s"', $dataset['ordprodid'] ) );
			}

			$ordProdAttr->setId( null );
			$ordProdAttr->setParentId( $ordProds[$dataset['ordprodid']] );
			$ordProdAttr->setCode( $dataset['code'] );
			$ordProdAttr->setValue( $dataset['value'] );
			$ordProdAttr->setName( $dataset['name'] );
			$ordProdAttr->setQuantity( $dataset['quantity'] );

			if( isset( $attrCodes[$dataset['code']] ) )
			{
				foreach( (array) $attrCodes[$dataset['code']] as $attrItem )
				{
					if( $attrItem->getCode() == $dataset['value'] ) {
						$ordProdAttr->setAttributeId( $attrItem->getId() );
					}
				}
			}

			if( isset( $dataset['type'] ) ) {
				$ordProdAttr->setType( $dataset['type'] );
			}

			$manager->saveItem( $ordProdAttr, false );
		}
	}


	/**
	 * Adds the order service attributes.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager
	 * @param array $testdata
	 * @param array $ordServices
	 * @throws \Aimeos\MW\Setup\Exception
	 */
	protected function addOrderBaseServiceAttributeData( \Aimeos\MShop\Common\Manager\Iface $manager,
		array $testdata, array $ordServices )
	{
		$ordServAttr = $manager->createItem();

		foreach( $testdata['order/base/service/attr'] as $dataset )
		{
			if( !isset( $ordServices[$dataset['ordservid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No order service ID found for "%1$s"', $dataset['ordservid'] ) );
			}

			$ordServAttr->setId( null );
			$ordServAttr->setParentId( $ordServices[$dataset['ordservid']] );
			$ordServAttr->setCode( $dataset['code'] );
			$ordServAttr->setValue( $dataset['value'] );
			$ordServAttr->setName( $dataset['name'] );
			$ordServAttr->setType( $dataset['type'] );
			$ordServAttr->setQuantity( $dataset['quantity'] );

			if( isset( $dataset['attrid'] ) ) {
				$ordServAttr->setAttributeId( $dataset['attrid'] );
			}

			$manager->saveItem( $ordServAttr, false );
		}
	}


	/**
	 * Adds the order test data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $orderManager Order manager
	 * @param array $baseIds List of ids
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function addOrderData( \Aimeos\MShop\Common\Manager\Iface $orderManager, array $baseIds, array $testdata )
	{
		$orderStatusManager = $orderManager->getSubManager( 'status', 'Standard' );

		$ords = [];
		$ordItem = $orderManager->createItem();

		$orderManager->begin();

		foreach( $testdata['order'] as $key => $dataset )
		{
			if( !isset( $baseIds[$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			$ordItem->setId( null );
			$ordItem->setType( $dataset['type'] );
			$ordItem->setBaseId( $baseIds[$dataset['baseid']] );
			$ordItem->setDeliveryStatus( $dataset['statusdelivery'] );
			$ordItem->setPaymentStatus( $dataset['statuspayment'] );
			$ordItem->setDateDelivery( $dataset['datedelivery'] );
			$ordItem->setDatePayment( $dataset['datepayment'] );
			$ordItem->setRelatedId( $dataset['relatedid'] );

			$orderManager->saveItem( $ordItem );
			$ords[$key] = $ordItem->getId();
		}

		$ordStat = $orderStatusManager->createItem();
		foreach( $testdata['order/status'] as $dataset )
		{
			if( !isset( $ords[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No order ID found for "%1$s"', $dataset['parentid'] ) );
			}

			$ordStat->setId( null );
			$ordStat->setParentId( $ords[$dataset['parentid']] );
			$ordStat->setType( $dataset['type'] );
			$ordStat->setValue( $dataset['value'] );

			$orderStatusManager->saveItem( $ordStat, false );
		}

		$orderManager->commit();
	}


	/**
	 * Returns the customer IDs for the given test data.
	 *
	 * @param array $testdata Test data
	 * @return array Customer Ids
	 */
	protected function getCustomerIds( array $testdata )
	{
		$customercodes = $customerIds = [];

		foreach( $testdata['order/base'] as $key => $dataset ) {
			$customercodes[] = $dataset['customerid'];
		}

		$customerManager = \Aimeos\MShop::create( $this->additional, 'customer' );
		$search = $customerManager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', $customercodes ) );

		foreach( $customerManager->search( $search ) as $id => $customerItem ) {
			$customerIds[$customerItem->getCode()] = $id;
		}

		return $customerIds;
	}


	/**
	 * Returns the product items for the given test data.
	 *
	 * @param array $testdata Test data
	 * @return \Aimeos\MShop\Product\Item\Iface[] Product Items
	 */
	protected function getProductItems( array $testdata )
	{
		$codes = $items = [];
		$productManager = \Aimeos\MShop::create( $this->additional, 'product' );

		foreach( $testdata['order/base/product'] as $key => $dataset )
		{
			if( isset( $dataset['prodid'] ) ) {
				$codes[$key] = $dataset['prodid'];
			}
		}

		$search = $productManager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );
		$result = $productManager->search( $search );

		foreach( $result as $item ) {
			$items[$item->getCode()] = $item;
		}

		return $items;
	}


	/**
	 * Returns the service item IDs for the given test data.
	 *
	 * @param array $testdata Test data
	 * @return array List of service IDs
	 */
	protected function getServiceIds( array $testdata )
	{
		$services = $servIds = [];
		$serviceManager = \Aimeos\MShop::create( $this->additional, 'service' );

		foreach( $testdata['order/base/service'] as $key => $dataset )
		{
			if( isset( $dataset['servid'] ) ) {
				$services[$key] = $dataset['servid'];
			}
		}

		$search = $serviceManager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', $services ) );
		$servicesResult = $serviceManager->search( $search );

		foreach( $servicesResult as $id => $service ) {
			$servIds[$service->getCode()] = $id;
		}

		return $servIds;
	}
}
