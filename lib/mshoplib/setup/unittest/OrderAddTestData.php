<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'PluginAddTestData', 'ProductAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'JobAddTestData', 'CatalogRebuildTestIndex' );
	}


	/**
	 * Adds order test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding order test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->additional, 'Standard' );
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->additional, 'Standard' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.sitecode', array( 'unittest', 'unit' ) ) );

		foreach( $orderBaseManager->searchItems( $search ) as $order ) {
			$orderBaseManager->deleteItem( $order->getId() );
		}


		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'order.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for order domain', $path ) );
		}

		$bases = $this->addOrderBaseData( $localeManager, $orderBaseManager, $testdata );
		$bases['items'] = $this->addOrderBaseProductData( $orderBaseManager, $bases, $testdata );
		$bases['items'] = $this->addOrderBaseServiceData( $orderBaseManager, $bases, $testdata );

		//update order bases (getPrice)
		foreach( $bases['items'] as $baseItem ) {
			$orderBaseManager->saveItem( $baseItem, false );
		}

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

		$this->conn->begin();

		foreach( $testdata['order/base'] as $key => $dataset )
		{
			$bases['items'][$key] = $orderBaseManager->createItem();
			$bases['items'][$key]->setId( null );
			$bases['items'][$key]->setComment( $dataset['comment'] );
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

		$this->conn->commit();

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
			$orderAddr->setAddressId( ( isset( $dataset['addrid'] ) ? $dataset['addrid'] : '' ) );
			$orderAddr->setType( $dataset['type'] );
			$orderAddr->setCompany( $dataset['company'] );
			$orderAddr->setVatID( ( isset( $dataset['vatid'] ) ? $dataset['vatid'] : '' ) );
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
			$orderAddr->setFlag( $dataset['flag'] );

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
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );
		$ordServ = $orderBaseServiceManager->createItem();

		$this->conn->begin();

		foreach( $testdata['order/base/service'] as $key => $dataset )
		{
			if( !isset( $bases['ids'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s" in order base serive data', $dataset['baseid'] ) );
			}

			if( !isset( $bases['items'][$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base Item found for "%1$s" in order base service data', $dataset['baseid'] ) );
			}

			$priceItem = $priceManager->createItem();

			$ordServ->setId( null );
			$ordServ->setBaseId( $bases['ids'][$dataset['baseid']] );
			$ordServ->setType( $dataset['type'] );
			$ordServ->setCode( $dataset['code'] );
			$ordServ->setName( $dataset['name'] );
			$ordServ->setMediaUrl( $dataset['mediaurl'] );

			if( isset( $dataset['servid'] ) ) {
				$ordServ->setServiceId( $servIds[$dataset['servid']] );
			}

			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRate( $dataset['taxrate'] );
			$ordServ->setPrice( $priceItem );

			$orderBaseServiceManager->saveItem( $ordServ );

			$ordServices[$key] = $ordServ->getId();
			$bases['items'][$dataset['baseid']]->setService( $ordServ, $dataset['type'] ); //adds Services to orderbase
		}

		$this->addOrderBaseServiceAttributeData( $orderBaseServiceAttrManager, $testdata, $ordServices );

		$this->conn->commit();

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
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );

		$this->conn->begin();

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

			if( isset( $dataset['prodid'] ) ) {
				$ordProdItem->setProductId( $products[$dataset['prodid']]->getId() );
			}

			// product bundle related fields
			if( isset( $dataset['ordprodid'] ) ) {
				$ordProdItem->setOrderProductId( $ordProds[$dataset['ordprodid']] );
			}

			$priceItem = $priceManager->createItem();
			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRate( $dataset['taxrate'] );
			$ordProdItem->setPrice( $priceItem );

			$orderBaseProductManager->saveItem( $ordProdItem );

			$bases['items'][$dataset['baseid']]->addProduct( $ordProdItem, $dataset['pos'] ); //adds Products to orderbase
			$ordProds[$key] = $ordProdItem->getId();
		}

		$this->addOrderBaseProductAttributeData( $orderBaseProductAttrManager, $testdata, $ordProds, $products );

		$this->conn->commit();

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
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->additional, 'Standard' );
		$attributes = $attributeManager->searchItems( $attributeManager->createSearch() );

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
				$ordProdAttr->setType( $products[$dataset['type']]->getType() );
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

		$this->conn->begin();

		foreach( $testdata['order'] as $key => $dataset )
		{
			if( !isset( $baseIds[$dataset['baseid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			$ordItem->setId( null );
			$ordItem->setBaseId( $baseIds[$dataset['baseid']] );
			$ordItem->setType( $dataset['type'] );
			$ordItem->setDateDelivery( $dataset['datedelivery'] );
			$ordItem->setDatePayment( $dataset['datepayment'] );
			$ordItem->setDeliveryStatus( $dataset['statusdelivery'] );
			$ordItem->setPaymentStatus( $dataset['statuspayment'] );
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

		$this->conn->commit();
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

		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->additional, 'Standard' );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $customercodes ) );

		foreach( $customerManager->searchItems( $search ) as $id => $customerItem ) {
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
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );

		foreach( $testdata['order/base/product'] as $key => $dataset )
		{
			if( isset( $dataset['prodid'] ) ) {
				$codes[$key] = $dataset['prodid'];
			}
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );
		$result = $productManager->searchItems( $search );

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
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->additional, 'Standard' );

		foreach( $testdata['order/base/service'] as $key => $dataset )
		{
			if( isset( $dataset['servid'] ) ) {
				$services[$key] = $dataset['servid'];
			}
		}

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', $services ) );
		$servicesResult = $serviceManager->searchItems( $search );

		foreach( $servicesResult as $id => $service ) {
			$servIds[$service->getCode()] = $id;
		}

		return $servIds;
	}
}
