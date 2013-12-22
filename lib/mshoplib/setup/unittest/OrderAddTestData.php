<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds order test data.
 */
class MW_Setup_Task_OrderAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData', 'PluginAddTestData', 'ProductAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'JobAddTestData', 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds order test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg('Adding order test data', 0);
		$this->_additional->setEditor( 'core:unittest' );

		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_additional, 'Default' );
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_additional, 'Default' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.sitecode', array('unittest', 'unit') ) );

		foreach( $orderBaseManager->searchItems( $search ) as $order ) {
			$orderBaseManager->deleteItem( $order->getId() );
		}


		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'order.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for order domain', $path ) );
		}

		$bases = $this->_addOrderBaseData( $localeManager, $orderBaseManager, $testdata );
		$bases['items'] = $this->_addOrderBaseProductData( $orderBaseManager, $bases, $testdata );
		$bases['items'] = $this->_addOrderBaseServiceData( $orderBaseManager, $bases, $testdata );

		//update order bases (getPrice)
		foreach( $bases['items'] as $baseItem) {
			$orderBaseManager->saveItem( $baseItem, false );
		}

		$this->_addOrderData( $orderManager, $bases['ids'], $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the required order base data.
	 *
	 * @param MShop_Locale_Manager_Interface $localeManager Locale Manager
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order Base Manager
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _addOrderBaseData( $localeManager, $orderBaseManager, array $testdata )
	{
		$orderBaseAddressManager = $orderBaseManager->getSubManager( 'address', 'Default' );

		foreach ($testdata['order/base'] as $key => $dataset) {
			$customercodes[] = $dataset['customerid'];
		}

		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_additional, 'Default' );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $customercodes ) );

		foreach( $customerManager->searchItems( $search ) as $id => $customerItem ) {
			$customerIds[$customerItem->getCode()] = $id;
		}

		$bases = array();
		$locale = $localeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['order/base'] as $key => $dataset )
		{
			$bases['items'][ $key ] = $orderBaseManager->createItem();
			$bases['items'][ $key ]->setId( null );
			$bases['items'][ $key ]->setComment( $dataset['comment'] );
			$bases['items'][ $key ]->setCustomerId( $customerIds[ $dataset['customerid'] ] );

			$locale->setId( null );
			$locale->setSiteId( $this->_additional->getLocale()->getSiteId() );
			$locale->setLanguageId( $dataset['langid'] );
			$locale->setCurrencyId( $dataset['currencyid'] );
			$bases['items'][ $key ]->setLocale( $locale );

			$orderBaseManager->saveItem( $bases['items'][ $key ] );
			$bases['ids'][ $key ] = $bases['items'][ $key ]->getId();
		}

		$order = array();
		$orderAddr = $orderBaseAddressManager->createItem();
		foreach( $testdata['order/base/address'] as $dataset )
		{
			if( !isset( $bases['ids'][ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			$orderAddr->setId( null );
			$orderAddr->setBaseId( $bases['ids'][ $dataset['baseid'] ] );
			$orderAddr->setAddressId( ( isset( $dataset['addrid'] ) ? $dataset['addrid'] : '' ) );
			$orderAddr->setType( $dataset['type'] );
			$orderAddr->setCompany( $dataset['company'] );
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
			$orderAddr->setFlag( $dataset['flag'] );

			$orderBaseAddressManager->saveItem( $orderAddr, false );
		}

		$this->_conn->commit();

		return $bases;
	}


	/**
	 * Adds the required order base service data.
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order Base Manager
	 * @param array $bases Associative list of key/list pairs
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _addOrderBaseServiceData( $orderBaseManager, array $bases, array $testdata )
	{
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service', 'Default' );
		$orderBaseServiceAttrManager = $orderBaseServiceManager->getSubManager( 'attribute', 'Default' );
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_additional, 'Default' );

		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_additional, 'Default' );
		$services = array();
		foreach( $testdata['order/base/service'] as $key => $dataset ) {
			if( isset( $dataset['servid'] ) ) {
				$services[$key] = $dataset['servid'];
			}
		}

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', $services ) );
		$servicesResult = $serviceManager->searchItems( $search );

		$servIds = array();
		foreach( $servicesResult as $id => $service ) {
			$servIds[$service->getCode()] = $id;
		}

		$ordServices = array ();
		$ordServ = $orderBaseServiceManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['order/base/service'] as $key => $dataset )
		{
			if( !isset( $bases['ids'][ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base ID found for "%1$s" in order base serive data', $dataset['baseid'] ) );
			}

			if( !isset( $bases['items'][ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base Item found for "%1$s" in order base service data', $dataset['baseid'] ) );
			}

			$priceItem = $priceManager->createItem();
			$ordServ->setId(null);
			$ordServ->setBaseId( $bases['ids'][ $dataset['baseid'] ] );

			if( isset( $dataset['servid'] ) ) {
				$ordServ->setServiceId( $servIds[$dataset['servid']] );
			}

			$ordServ->setType($dataset['type']);
			$ordServ->setCode($dataset['code']);
			$ordServ->setName($dataset['name']);
			$ordServ->setMediaUrl($dataset['mediaurl']);

			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRate( $dataset['taxrate'] );
			$ordServ->setPrice( $priceItem );

			$orderBaseServiceManager->saveItem( $ordServ );

			$ordServices[ $key ] = $ordServ->getId();
			$bases['items'][ $dataset['baseid'] ]->setService( $ordServ, $dataset['type'] ); //adds Services to orderbase
		}

		$ordServAttr = $orderBaseServiceAttrManager->createItem();
		foreach( $testdata['order/base/service/attr'] as $dataset )
		{
			if( !isset( $ordServices[ $dataset['ordservid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No order service ID found for "%1$s"', $dataset['ordservid'] ) );
			}

			$ordServAttr->setId( null );
			$ordServAttr->setServiceId( $ordServices[ $dataset['ordservid'] ] );
			$ordServAttr->setCode( $dataset['code'] );
			$ordServAttr->setValue( $dataset['value'] );
			$ordServAttr->setName( $dataset['name'] );
			$ordServAttr->setType( $dataset['type'] );

			if( isset( $dataset['attrid'] ) ) {
				$ordServAttr->setAttributeId( $dataset['attrid'] );
			}

			$orderBaseServiceAttrManager->saveItem( $ordServAttr, false );
		}

		$this->_conn->commit();

		return $bases['items'];
	}


	/**
	 * Adds the required order base product data.
	 *
	 * @param MShop_Order_Manager_Base_Interface $orderBaseManager Order Base Manager
	 * @param array $bases Associative list of key/list pairs
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _addOrderBaseProductData( $orderBaseManager, array $bases, array $testdata )
	{
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product', 'Default' );
		$orderBaseProductAttrManager = $orderBaseProductManager->getSubManager( 'attribute', 'Default' );
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_additional, 'Default' );
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_additional, 'Default' );
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_additional, 'Default' );

		$products = array();
		foreach( $testdata['order/base/product'] as $key => $dataset ) {
			if( isset( $dataset['prodid'] ) ) {
				$products[$key] = $dataset['prodid'];
			}
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $products ) );
		$productsResult = $productManager->searchItems( $search );

		$prodIds = array();
		$prodTypes = array();
		foreach( $productsResult as $id => $product )
		{
			$prodIds[$product->getCode()] = $id;
			$prodTypes[$product->getCode()] = $product->getType();
		}

		$ordProds = $prices = array();

		$this->_conn->begin();

		foreach( $testdata['order/base/product'] as $key => $dataset )
		{
			if( !isset( $bases['ids'][ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base ID found for "%1$s" in order base product data', $dataset['baseid'] ) );
			}

			if( !isset( $bases['items'][ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base Item found for "%1$s" in order base product data', $dataset['baseid'] ) );
			}

			$ordProdItem = $orderBaseProductManager->createItem();
			$prices[$dataset['prodcode'].'/'.$dataset['baseid']] = $priceManager->createItem();
			$ordProdItem->setId(null);
			$ordProdItem->setBaseId( $bases['ids'][ $dataset['baseid'] ] );

			if( isset( $dataset['prodid'] ) ) {
				$ordProdItem->setProductId( $prodIds[$dataset['prodid']] );
			}

			// product bundle related fields
			if( isset( $dataset['ordprodid'] ) ){
				$ordProdItem->setOrderProductId( $ordProds[ $dataset['ordprodid'] ] );
			}
			if( isset( $dataset['type'] ) ) {
				$ordProdItem->setType( $dataset['type'] );
			}
			$ordProdItem->setSupplierCode( $dataset['suppliercode'] );
			$ordProdItem->setProductCode( $dataset['prodcode'] );
			$ordProdItem->setName( $dataset['name'] );
			$ordProdItem->setMediaUrl( $dataset['mediaurl'] );
			$ordProdItem->setQuantity( $dataset['amount'] );
			$ordProdItem->setFlags( $dataset['flags'] );
			$ordProdItem->setStatus( $dataset['status'] );

			$priceItem = $priceManager->createItem();
			$priceItem->setValue( $dataset['price'] );
			$priceItem->setCosts( $dataset['shipping'] );
			$priceItem->setRebate( $dataset['rebate'] );
			$priceItem->setTaxRate( $dataset['taxrate'] );
			$ordProdItem->setPrice( $priceItem );

			$orderBaseProductManager->saveItem( $ordProdItem );
			$bases['items'][ $dataset['baseid'] ]->addProduct( $ordProdItem, $dataset['pos'] ); //adds Products to orderbase
			$ordProds[ $key ] = $ordProdItem->getId();
		}


		$attrCodes = array();
		foreach( $attributeManager->searchItems( $attributeManager->createSearch() ) as $attrItem ) {
			$attrCodes[ $attrItem->getType() ][] = $attrItem;
		}

		$ordProdAttr = $orderBaseProductAttrManager->createItem();
		foreach ( $testdata['order/base/product/attr'] as $dataset )
		{
			if( !isset( $ordProds[ $dataset['ordprodid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No order product ID found for "%1$s"', $dataset['ordprodid'] ) );
			}

			$ordProdAttr->setId( null );
			$ordProdAttr->setProductId( $ordProds[ $dataset['ordprodid'] ] );
			$ordProdAttr->setCode( $dataset['code'] );
			$ordProdAttr->setValue( $dataset['value'] );
			$ordProdAttr->setName( $dataset['name'] );

			if( isset( $attrCodes[ $dataset['code'] ] ) )
			{
				foreach( $attrCodes[ $dataset['code'] ] as $attrItem )
				{
					if( $attrItem->getCode() == $dataset['value'] ) {
						$ordProdAttr->setAttributeId( $attrItem->getId() );
					}
				}
			}

			if( isset( $dataset['type'] ) ) {
				$ordProdAttr->setType( $prodTypes[$dataset['type']] );
			}

			$orderBaseProductAttrManager->saveItem( $ordProdAttr, false );
		}

		$this->_conn->commit();

		return $bases['items'];
	}


	/**
	 * Adds the order test data.
	 *
	 * @param MShop_Order_Manager_Interface $orderManager Order Manager
	 * @param array $baseIds List of ids
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function _addOrderData( $orderManager, array $baseIds, array $testdata )
	{
		$orderStatusManager = $orderManager->getSubManager( 'status', 'Default' );

		$ords = array();
		$ordItem = $orderManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['order'] as $key => $dataset )
		{
			if( !isset( $baseIds[ $dataset['baseid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			$ordItem->setId( null );
			$ordItem->setBaseId( $baseIds[ $dataset['baseid'] ] );
			$ordItem->setType( $dataset['type'] );
			$ordItem->setDateDelivery( $dataset['datedelivery'] );
			$ordItem->setDatePayment( $dataset['datepayment'] );
			$ordItem->setDeliveryStatus( $dataset['statusdelivery'] );
			$ordItem->setPaymentStatus( $dataset['statuspayment'] );
			$ordItem->setRelatedId( $dataset['relatedid'] );

			$orderManager->saveItem( $ordItem );
			$ords[ $key ] = $ordItem->getId();
		}

		$ordStat = $orderStatusManager->createItem();
		foreach( $testdata['order/status'] as $dataset )
		{
			if( !isset( $ords[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No order ID found for "%1$s"', $dataset['parentid'] ) );
			}

			$ordStat->setId( null );
			$ordStat->setParentId( $ords[ $dataset['parentid'] ] );
			$ordStat->setType( $dataset['type'] );
			$ordStat->setValue( $dataset['value'] );

			$orderStatusManager->saveItem( $ordStat, false );
		}

		$this->_conn->commit();
	}
}
