<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Default implementation of the basket frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Basket_Default
	extends Controller_Frontend_Abstract
	implements Controller_Frontend_Basket_Interface
{
	private $_basket;
	private $_domainManager;
	private $_listTypeAttributes = array();


	/**
	 * Initializes the frontend controller.
	 *
	 * @param MShop_Context_Item_Interface $context Object storing the required instances for manaing databases
	 *  connections, logger, session, etc.
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_domainManager = MShop_Factory::createManager( $context, 'order/base' );
		$this->_basket = $this->_domainManager->getSession();

		$this->_checkLocale();
	}


	/**
	 * Empties the basket and removing all products, addresses, services, etc.
	 */
	public function clear()
	{
		$this->_basket = $this->_domainManager->createItem();
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Returns the basket object.
	 *
	 * @return MShop_Order_Item_Base_Interface Basket holding products, addresses and delivery/payment options
	 */
	public function get()
	{
		return $this->_basket;
	}


	/**
	 * Adds a categorized product to the basket of the user stored in the session.
	 *
	 * @param string $prodid ID of the base product to add
	 * @param integer $quantity Amount of products that should by added
	 * @param array $options Possible options are: 'stock'=>true|false and 'variant'=>true|false
	 * 	The 'stock'=>false option allows adding products without being in stock.
	 * 	The 'variant'=>false option allows adding the selection product to the basket
	 * 	instead of the specific sub-product if the variant-building attribute IDs
	 * 	doesn't match a specific sub-product or if the attribute IDs are missing.
	 * @param array $variantAttributeIds List of variant-building attribute IDs that identify a specific product
	 * 	in a selection products
	 * @param array $configAttributeIds  List of attribute IDs that doesn't identify a specific product in a
	 * 	selection of products but are stored together with the product (e.g. for configurable products)
	 * @param array $hiddenAttributeIds List of attribute IDs that should be stored along with the product in the order
	 * @param array $customAttributeValues Associative list of attribute IDs and arbitrary values that should be stored
	 * 	along with the product in the order
	 * @param string $warehouse Unique code of the warehouse to deliver the products from
	 * @throws Controller_Frontend_Basket_Exception If the product isn't available
	 */
	public function addProduct( $prodid, $quantity = 1, array $options = array(), array $variantAttributeIds = array(),
		array $configAttributeIds = array(), array $hiddenAttributeIds = array(), array $customAttributeValues = array(),
		$warehouse = 'default' )
	{
		$this->_checkCategory( $prodid );

		$context = $this->_getContext();

		$productManager = MShop_Factory::createManager( $context, 'product' );
		$productItem = $productManager->getItem( $prodid, array( 'media', 'price', 'product', 'text' ) );

		$orderBaseProductItem = MShop_Factory::createManager( $context, 'order/base/product' )->createItem();
		$orderBaseProductItem->copyFrom( $productItem );
		$orderBaseProductItem->setQuantity( $quantity );
		$orderBaseProductItem->setWarehouseCode( $warehouse );

		$attr = array();
		$prices = $productItem->getRefItems( 'price', 'default', 'default' );

		switch( $productItem->getType() ) {
			case 'select':
				$attr = $this->_getVariantDetails( $orderBaseProductItem, $productItem, $prices, $variantAttributeIds, $options );
				break;
			case 'bundle':
				$this->_addBundleProducts( $orderBaseProductItem, $productItem, $variantAttributeIds, $warehouse );
				break;
		}

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$price = $priceManager->getLowestPrice( $prices, $quantity );

		$attr = array_merge( $attr, $this->_createOrderProductAttributes( $price, $prodid, $quantity, $configAttributeIds, 'config' ) );
		$attr = array_merge( $attr, $this->_createOrderProductAttributes( $price, $prodid, $quantity, $hiddenAttributeIds, 'hidden' ) );
		$attr = array_merge( $attr, $this->_createOrderProductAttributes( $price, $prodid, $quantity, array_keys( $customAttributeValues ), 'custom', $customAttributeValues ) );

		// remove product rebate of original price in favor to rebates granted for the order
		$price->setRebate( '0.00' );

		$orderBaseProductItem->setPrice( $price );
		$orderBaseProductItem->setAttributes( $attr );

		$this->_addProductInStock( $orderBaseProductItem, $productItem->getId(), $quantity, $options, $warehouse );
	}


	/**
	 * Deletes a product item from the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 */
	public function deleteProduct( $position )
	{
		$product = $this->_basket->getProduct( $position );

		if( $product->getFlags() === MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE )
		{
			$msg = sprintf( 'Basket item at position "%1$d" cannot be deleted manually', $position );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		$this->_basket->deleteProduct( $position );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Edits the quantity of a product item in the basket.
	 *
	 * @param integer $position Position number (key) of the order product item
	 * @param integer $quantity New quantiy of the product item
	 * @param array $options Possible options are: 'stock'=>true|false
	 * 	The 'stock'=>false option allows adding products without being in stock.
	 * @param string[] $configAttributeCodes Codes of the product config attributes that should be REMOVED
	 */
	public function editProduct( $position, $quantity, array $options = array(),
		array $configAttributeCodes = array() )
	{
		$product = $this->_basket->getProduct( $position );
		$product->setQuantity( $quantity ); // Enforce check immediately

		if( $product->getFlags() & MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE )
		{
			$msg = sprintf( 'Basket item at position "%1$d" cannot be changed', $position );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		$attributes = $product->getAttributes();
		foreach( $attributes as $key => $attribute )
		{
			if( in_array( $attribute->getCode(), $configAttributeCodes ) ) {
				unset( $attributes[$key] );
			}
		}
		$product->setAttributes( $attributes );

		$productItem = $this->_getProductItem( $product->getProductCode() );
		$prices = $productItem->getRefItems( 'price', 'default' );

		$product->setPrice( $this->_calcPrice( $product, $prices, $quantity ) );

		$this->_editProductInStock( $product, $productItem, $quantity, $position, $options );
	}


	/**
	 * Adds the given coupon code and updates the basket.
	 *
	 * @param string $code Coupon code entered by the user
	 * @throws Controller_Frontend_Basket_Exception if the coupon code is invalid or not allowed
	 */
	public function addCoupon( $code )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'coupon' );
		$codeManager = MShop_Factory::createManager( $this->_getContext(), 'coupon/code' );


		$search = $codeManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'coupon.code.code', $code ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$result = $codeManager->searchItems( $search );

		if( ( $codeItem = reset( $result ) ) === false ) {
			throw new Controller_Frontend_Basket_Exception( sprintf( 'Coupon code "%1$s" is invalid or not available any more', $code ) );
		}


		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'coupon.id', $codeItem->getCouponId() ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Controller_Frontend_Basket_Exception( sprintf( 'Coupon for code "%1$s" is not available any more', $code ) );
		}


		$provider = $manager->getProvider( $item, $code );

		if( $provider->isAvailable( $this->_basket ) !== true ) {
			throw new Controller_Frontend_Basket_Exception( sprintf( 'Requirements for coupon code "%1$s" aren\'t met', $code ) );
		}

		$provider->addCoupon( $this->_basket );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Removes the given coupon code and its effects from the basket.
	 *
	 * @param string $code Coupon code entered by the user
	 * @throws Controller_Frontend_Basket_Exception if the coupon code is invalid
	 */
	public function deleteCoupon( $code )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'coupon' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', $code ) );
		$search->setSlice( 0, 1 );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Controller_Frontend_Basket_Exception( sprintf( 'Coupon code "%1$s" is invalid', $code ) );
		}

		$manager->getProvider( $item, $code )->deleteCoupon( $this->_basket );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Sets the address of the customer in the basket.
	 *
	 * @param string $type Address type constant from MShop_Order_Item_Base_Address_Abstract
	 * @param MShop_Common_Item_Address_Interface|array|null $value Address object or array with key/value pairs of address or null to remove address from basket
	 * @throws Controller_Frontend_Basket_Exception If the billing or delivery address is not of any required type of
	 * 	if one of the keys is invalid when using an array with key/value pairs
	 */
	public function setAddress( $type, $value )
	{
		$address = MShop_Factory::createManager( $this->_getContext(), 'order/base/address' )->createItem();
		$address->setType( $type );

		if( $value instanceof MShop_Common_Item_Address_Interface )
		{
			$address->copyFrom( $value );
			$this->_basket->setAddress( $address, $type );
		}
		else if( is_array( $value ) )
		{
			$this->_setAddressFromArray( $address, $value );
			$this->_basket->setAddress( $address, $type );
		}
		else if( $value === null )
		{
			$this->_basket->deleteAddress( $type );
		}
		else
		{
			throw new Controller_Frontend_Basket_Exception( sprintf( 'Invalid value for address type "%1$s"', $type ) );
		}

		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Sets the delivery/payment service item based on the service ID.
	 *
	 * @param string $type Service type code like 'payment' or 'delivery'
	 * @param string $id Unique ID of the service item
	 * @param array $attributes Associative list of key/value pairs containing the attributes selected or
	 * 	entered by the customer when choosing one of the delivery or payment options
	 * @throws Controller_Frontend_Basket_Exception If there is no price to the service item attached
	 */
	public function setService( $type, $id, array $attributes = array() )
	{
		$context = $this->_getContext();

		$serviceManager = MShop_Factory::createManager( $context, 'service' );
		$serviceItem = $serviceManager->getItem( $id, array( 'media', 'price', 'text' ) );

		$provider = $serviceManager->getProvider( $serviceItem );
		$result = $provider->checkConfigFE( $attributes );
		$unknown = array_diff_key( $attributes, $result );

		if( count( $unknown ) > 0 )
		{
			$msg = sprintf( 'Unknown attributes "%1$s"', implode( '","', array_keys( $unknown ) ) );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		foreach( $result as $key => $value )
		{
			if( $value !== null ) {
				throw new Controller_Frontend_Basket_Exception( $value );
			}
		}

		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );
		$orderServiceItem = $orderBaseServiceManager->createItem();
		$orderServiceItem->copyFrom( $serviceItem );

		$price = $provider->calcPrice( $this->_basket );
		// remove service rebate of original price
		$price->setRebate( '0.00' );
		$orderServiceItem->setPrice( $price );

		$provider->setConfigFE( $orderServiceItem, $attributes );

		$this->_basket->setService( $orderServiceItem, $type );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Edits the changed product to the basket if it's in stock.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $orderBaseProductItem Old order product from basket
	 * @param string $productId Unique ID of the product item that belongs to the order product
	 * @param integer $quantity New order quantity
	 * @param integer $position Position of the old order product in the basket
	 * @param array Associative list of options
	 * @throws Controller_Frontend_Basket_Exception If there's not enough stock available
	 */
	private function _addProductInStock( MShop_Order_Item_Base_Product_Interface $orderBaseProductItem,
		$productId, $quantity, array $options, $warehouse )
	{
		$stocklevel = null;
		if( !isset( $options['stock'] ) || $options['stock'] != false ) {
			$stocklevel = $this->_getStockLevel( $productId, $warehouse );
		}

		if( $stocklevel === null || $stocklevel > 0 )
		{
			$position = $this->_basket->addProduct( $orderBaseProductItem );
			$orderBaseProductItem = clone $this->_basket->getProduct( $position );
			$quantity = $orderBaseProductItem->getQuantity();

			if( $stocklevel > 0 && $stocklevel < $quantity )
			{
				$this->_basket->deleteProduct( $position );
				$orderBaseProductItem->setQuantity( $stocklevel );
				$this->_basket->addProduct( $orderBaseProductItem, $position );
			}
		}

		$this->_domainManager->setSession( $this->_basket );

		if( $stocklevel !== null && $stocklevel < $quantity )
		{
			$msg = sprintf( 'There are not enough products "%1$s" in stock', $orderBaseProductItem->getName() );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
	}


	/**
	 * Edits the changed product to the basket if it's in stock.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $product Old order product from basket
	 * @param MShop_Product_Item_Interface $productItem Product item that belongs to the order product
	 * @param integer $quantity New order quantity
	 * @param integer $position Position of the old order product in the basket
	 * @param array Associative list of options
	 * @throws Controller_Frontend_Basket_Exception If there's not enough stock available
	 */
	private function _editProductInStock( MShop_Order_Item_Base_Product_Interface $product,
		MShop_Product_Item_Interface $productItem, $quantity, $position, array $options )
	{
		$stocklevel = null;
		if( !isset( $options['stock'] ) || $options['stock'] != false ) {
			$stocklevel = $this->_getStockLevel( $productItem->getId(), $product->getWarehouseCode() );
		}

		$product->setQuantity( ( $stocklevel !== null && $stocklevel > 0 ? min( $stocklevel, $quantity ) : $quantity ) );

		$this->_basket->deleteProduct( $position );

		if( $stocklevel === null || $stocklevel > 0 )
		{
			$this->_basket->addProduct( $product, $position );
			$this->_domainManager->setSession( $this->_basket );
		}

		if( $stocklevel !== null && $stocklevel < $quantity )
		{
			$msg = sprintf( 'There are not enough products "%1$s" in stock', $productItem->getName() );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
	}


	/**
	 * Adds the bundled products to the order product item.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $orderBaseProductItem Order product item
	 * @param MShop_Product_Item_Interface $productItem Bundle product item
	 * @param array $variantAttributeIds List of product variant attribute IDs
	 */
	protected function _addBundleProducts( MShop_Order_Item_Base_Product_Interface $orderBaseProductItem,
		MShop_Product_Item_Interface $productItem, array $variantAttributeIds, $warehouse )
	{
		$quantity = $orderBaseProductItem->getQuantity();
		$products = $subProductIds = $orderProducts = array();
		$orderProductManager = MShop_Factory::createManager( $this->_getContext(), 'order/base/product' );

		foreach( $productItem->getRefItems( 'product', null, 'default' ) as $item ) {
			$subProductIds[] = $item->getId();
		}

		if( count( $subProductIds ) > 0 )
		{
			$productManager = MShop_Factory::createManager( $this->_getContext(), 'product' );

			$search = $productManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'product.id', $subProductIds ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$products = $productManager->searchItems( $search, array( 'attribute', 'media', 'price', 'text' ) );
		}

		foreach( $products as $product )
		{
			$prices = $product->getRefItems( 'price', 'default', 'default' );

			$orderProduct = $orderProductManager->createItem();
			$orderProduct->copyFrom( $product );
			$orderProduct->setWarehouseCode( $warehouse );
			$orderProduct->setPrice( $this->_calcPrice( $orderProduct, $prices, $quantity ) );

			$orderProducts[] = $orderProduct;
		}

		$orderBaseProductItem->setProducts( $orderProducts );
	}


	/**
	 * Checks if the product is part of at least one category in the product catalog.
	 *
	 * @param string $prodid Unique ID of the product
	 * @throws Controller_Frontend_Basket_Exception If product is not associated to at least one category
	 */
	protected function _checkCategory( $prodid )
	{
		$catalogListManager = MShop_Factory::createManager( $this->_getContext(), 'catalog/list' );

		$search = $catalogListManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'catalog.list.refid', $prodid ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$result = $catalogListManager->searchItems( $search );

		if( reset( $result ) === false )
		{
			$msg = sprintf( 'Adding product with ID "%1$s" is not allowed', $prodid );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
	}


	/**
	 * Checks for a locale mismatch and migrates the products to the new basket if necessary.
	 *
	 * @throws Controller_Basket_Exception If one or more products couldn't migrated
	 */
	protected function _checkLocale()
	{
		$errors = array();
		$context = $this->_getContext();
		$session = $context->getSession();
		$locale = $this->_basket->getLocale();

		$localeStr = $session->get( 'arcavias/basket/locale' );
		$localeKey = $locale->getSite()->getCode() . '|' . $locale->getLanguageId() . '|' . $locale->getCurrencyId();

		if( $localeStr !== null && $localeStr !== $localeKey )
		{
			$locParts = explode( '|', $localeStr );
			$locSite = ( isset( $locParts[0] ) ? $locParts[0] : '' );
			$locLanguage = ( isset( $locParts[1] ) ? $locParts[1] : '' );
			$locCurrency = ( isset( $locParts[2] ) ? $locParts[2] : '' );

			$localeManager = MShop_Factory::createManager( $context, 'locale' );
			$locale = $localeManager->bootstrap( $locSite, $locLanguage, $locCurrency, false );

			$context = clone $context;
			$context->setLocale( $locale );

			$manager = MShop_Order_Manager_Factory::createManager( $context )->getSubManager( 'base' );
			$basket = $manager->getSession();

			$errors = $this->_copyAddresses( $basket, $errors, $localeKey );
			$errors = $this->_copyServices( $basket, $errors );
			$errors = $this->_copyProducts( $basket, $errors, $localeKey );
			$errors = $this->_copyCoupons( $basket, $errors, $localeKey );

			$manager->setSession( $basket );
		}

		$session->set( 'arcavias/basket/locale', $localeKey );

		if( !empty( $errors ) )
		{
			$msg = $context->getI18n()->dt(
				'controller/frontend',
				sprintf( 'One or more items aren\'t available for the chosen locale' )
			);
			throw new Controller_Frontend_Basket_Exception( $msg, 0, null, $errors );
		}
	}


	/**
	 * Migrates the addresses from the old basket to the current one.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @param array $errors Associative list of previous errors
	 * @param string $localeKey Unique identifier of the site, language and currency
	 * @return array Associative list of errors occured
	 */
	private function _copyAddresses( MShop_Order_Item_Base_Interface $basket, array $errors, $localeKey )
	{
		foreach( $basket->getAddresses() as $type => $item )
		{
			try
			{
				$this->setAddress( $type, $item->toArray() );
				$basket->deleteAddress( $type );
			}
			catch( Exception $e )
			{
				$logger = $this->_getContext()->getLogger();
				$str = 'Error migrating address with type "%1$s" in basket to locale "%2$s": %3$s';
				$logger->log( sprintf( $str, $type, $localeKey, $e->getMessage() ), MW_Logger_Abstract::INFO );
				$errors['address'][$type] = $e->getMessage();
			}
		}

		return $errors;
	}


	/**
	 * Migrates the coupons from the old basket to the current one.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @param array $errors Associative list of previous errors
	 * @param string $localeKey Unique identifier of the site, language and currency
	 * @return array Associative list of errors occured
	 */
	private function _copyCoupons( MShop_Order_Item_Base_Interface $basket, array $errors, $localeKey )
	{
		foreach( $basket->getCoupons() as $code => $list )
		{
			try
			{
				$this->addCoupon( $code );
				$basket->deleteCoupon( $code, true );
			}
			catch( Exception $e )
			{
				$logger = $this->_getContext()->getLogger();
				$str = 'Error migrating coupon with code "%1$s" in basket to locale "%2$s": %3$s';
				$logger->log( sprintf( $str, $code, $localeKey, $e->getMessage() ), MW_Logger_Abstract::INFO );
				$errors['coupon'][$code] = $e->getMessage();
			}
		}

		return $errors;
	}


	/**
	 * Migrates the products from the old basket to the current one.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @param array $errors Associative list of previous errors
	 * @param string $localeKey Unique identifier of the site, language and currency
	 * @return array Associative list of errors occured
	 */
	private function _copyProducts( MShop_Order_Item_Base_Interface $basket, array $errors, $localeKey )
	{
		foreach( $basket->getProducts() as $pos => $product )
		{
			if( $product->getFlags() & MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE ) {
				continue;
			}

			try
			{
				$attrIds = array();

				foreach( $product->getAttributes() as $attrItem ) {
					$attrIds[ $attrItem->getType() ][] = $attrItem->getAttributeId();
				}

				$this->addProduct(
					$product->getProductId(),
					$product->getQuantity(),
					array(),
					$this->_getValue( $attrIds, 'variant', array() ),
					$this->_getValue( $attrIds, 'config', array() ),
					$this->_getValue( $attrIds, 'hidden', array() ),
					$product->getWarehouseCode()
				);

				$basket->deleteProduct( $pos );
			}
			catch( Exception $e )
			{
				$code = $product->getProductCode();
				$logger = $this->_getContext()->getLogger();
				$str = 'Error migrating product with code "%1$s" in basket to locale "%2$s": %3$s';
				$logger->log( sprintf( $str, $code, $localeKey, $e->getMessage() ), MW_Logger_Abstract::INFO );
				$errors['product'][$pos] = $e->getMessage();
			}
		}

		return $errors;
	}


	/**
	 * Migrates the services from the old basket to the current one.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @param array $errors Associative list of previous errors
	 * @return array Associative list of errors occured
	 */
	private function _copyServices( MShop_Order_Item_Base_Interface $basket, array $errors )
	{
		foreach( $basket->getServices() as $type => $item )
		{
			try
			{
				$attributes = array();

				foreach( $item->getAttributes() as $attrItem ) {
					$attributes[ $attrItem->getCode() ] = $attrItem->getValue();
				}

				$this->setService( $type, $item->getServiceId(), $attributes );
				$basket->deleteService( $type );
			}
			catch( Exception $e ) { ; } // Don't notify the user as appropriate services can be added automatically
		}

		return $errors;
	}


	/**
	 * Checks if the IDs of the given items are really associated to the product.
	 *
	 * @param string $prodId Unique ID of the product
	 * @param string $domain Domain the references must be of
	 * @param integer $listTypeId ID of the list type the referenced items must be
	 * @param array $refIds List of IDs that must be associated to the product
	 * @throws Controller_Frontend_Basket_Exception If one or more of the IDs are not associated
	 */
	protected function _checkReferences( $prodId, $domain, $listTypeId, array $refIds )
	{
		$productManager = MShop_Factory::createManager( $this->_getContext(), 'product' );
		$search = $productManager->createSearch( true );

		$expr = array(
			$search->compare( '==', 'product.id', $prodId ),
			$search->getConditions(),
		);

		if( count( $refIds ) > 0 )
		{
			$param = array( $domain, $listTypeId, $refIds );
			$cmpfunc = $search->createFunction( 'product.contains', $param );

			$expr[] = $search->compare( '==', $cmpfunc, count( $refIds ) );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );

		if( count( $productManager->searchItems( $search, array() ) ) === 0 )
		{
			$msg = sprintf( 'Invalid "%1$s" references for product with ID "%2$s"', $domain, $prodId );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
	}


	/**
	 * Returns the highest stock level for the product.
	 *
	 * @param string $prodid Unique ID of the product
	 * @param string $warehouse Unique code of the warehouse
	 * @return integer|null Number of available items in stock (null for unlimited stock)
	 */
	protected function _getStockLevel( $prodid, $warehouse )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/stock' );

		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.stock.productid', $prodid ),
			$search->getConditions(),
			$search->compare( '==', 'product.stock.warehouse.code', $warehouse ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( empty( $result ) )
		{
			$msg = sprintf( 'No stock for product ID "%1$s" and warehouse "%2$s" available', $prodid, $warehouse );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		$stocklevel = null;

		foreach( $result as $item )
		{
			if( ( $stock = $item->getStockLevel() ) === null ) {
				return null;
			}

			$stocklevel = max( (int) $stocklevel, $item->getStockLevel() );
		}

		return $stocklevel;
	}


	/**
	 * Creates the order product attribute items from the given attribute IDs and updates the price item if necessary.
	 *
	 * @param MShop_Price_Item_Interface $price Price item of the ordered product
	 * @param string $prodid Unique product ID where the given attributes must be attached to
	 * @param integer $quantity Number of products that should be added to the basket
	 * @param array $attributeIds List of attributes IDs of the given type
	 * @param string $type Attribute type
	 * @return array List of items implementing MShop_Order_Item_Product_Attribute_Interface
	 */
	protected function _createOrderProductAttributes( MShop_Price_Item_Interface $price, $prodid, $quantity,
		array $attributeIds, $type, array $attributeValues = array() )
	{
		if( empty( $attributeIds ) ) {
			return array();
		}

		$attrTypeId = $this->_getProductListTypeItem( 'attribute', $type )->getId();
		$this->_checkReferences( $prodid, 'attribute', $attrTypeId, $attributeIds );

		$list = array();
		$context = $this->_getContext();

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$orderProductAttributeManager = MShop_Factory::createManager( $context, 'order/base/product/attribute' );

		foreach( $this->_getAttributes( $attributeIds ) as $id => $attrItem )
		{
			$prices = $attrItem->getRefItems( 'price', 'default', 'default' );

			if( !empty( $prices ) ) {
				$price->addItem( $priceManager->getLowestPrice( $prices, $quantity ) );
			}

			$item = $orderProductAttributeManager->createItem();
			$item->copyFrom( $attrItem );
			$item->setType( $type );

			if( isset( $attributeValues[$id] ) ) {
				$item->setValue( $attributeValues[$id] );
			}

			$list[] = $item;
		}

		return $list;
	}


	/**
	 * Fills the order address object with the values from the array.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $address Address item to store the values into
	 * @param array $map Associative array of key/value pairs. The keys must be the same as when calling toArray() from
	 * 	an address item.
	 * @throws Controller_Frontend_Basket_Exception
	 */
	protected function _setAddressFromArray( MShop_Order_Item_Base_Address_Interface $address, array $map )
	{
		foreach( $map as $key => $value ) {
			$map[$key] = strip_tags( $value ); // prevent XSS
		}

		$errors = $address->fromArray( $map );

		if( count( $errors ) > 0 )
		{
			$msg = sprintf( 'Invalid address properties, please check your input' );
			throw new Controller_Frontend_Basket_Exception( $msg, 0, null, $errors );
		}
	}


	/**
	 * Returns the attribute items using the given order attribute items.
	 *
	 * @param MShop_Order_Item_Base_Product_Attribute_Item[] $orderAttributes List of order product attribute items
	 * @return MShop_Attribute_Item_Interface[] Associative list of attribute IDs as key and attribute items as values
	 */
	private function _getAttributeItems( array $orderAttributes )
	{
		if( empty( $orderAttributes ) ) {
			return array();
		}

		$attributeManager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );
		$search = $attributeManager->createSearch( true );
		$expr = array();

		foreach( $orderAttributes as $item )
		{
			$tmp = array(
				$search->compare( '==', 'attribute.domain', 'product' ),
				$search->compare( '==', 'attribute.code', $item->getValue() ),
				$search->compare( '==', 'attribute.type.domain', 'product' ),
				$search->compare( '==', 'attribute.type.code', $item->getCode() ),
				$search->compare( '>', 'attribute.type.status', 0 ),
				$search->getConditions(),
			);
			$expr[] = $search->combine( '&&', $tmp );
		}

		$search->setConditions( $search->combine( '||', $expr ) );
		return $attributeManager->searchItems( $search, array( 'price' ) );
	}


	/**
	 * Returns the attribute items for the given attribute IDs.
	 *
	 * @param array $attributeIds List of attribute IDs
	 * @param string[] $domains Names of the domain items that should be fetched too
	 * @return array List of items implementing MShop_Attribute_Item_Interface
	 * @throws Controller_Frontend_Basket_Exception If the actual attribute number doesn't match the expected one
	 */
	protected function _getAttributes( array $attributeIds, array $domains = array( 'price', 'text' ) )
	{
		if( empty( $attributeIds ) ) {
			return array();
		}

		$attributeManager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );

		$search = $attributeManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.id', $attributeIds ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		$attrItems = $attributeManager->searchItems( $search, $domains );

		if( count( $attrItems ) !== count( $attributeIds ) )
		{
			$expected = implode( ',', $attributeIds );
			$actual = implode( ',', array_keys( $attrItems ) );
			$msg = sprintf( 'Available attribute IDs "%1$s" do not match the given attribute IDs "%2$s"', $actual, $expected );

			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		return $attrItems;
	}


	/**
	 * Calculates and returns the current price for the given order product and product prices.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $product Ordered product item
	 * @param MShop_Price_Item_Interface[] $prices List of price items
	 * @param integer $quantity New product quantity
	 * @return MShop_Price_Item_Interface Price item with calculated price
	 */
	private function _calcPrice( MShop_Order_Item_Base_Product_Interface $product, array $prices, $quantity )
	{
		$context = $this->_getContext();

		if( empty( $prices ) )
		{
			$productManager = MShop_Factory::createManager( $context, 'product' );
			$parentItem = $productManager->getItem( $product->getProductId(), array( 'price' ) );
			$prices = $parentItem->getRefItems( 'price', 'default' );
		}

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$price = $priceManager->getLowestPrice( $prices, $quantity );

		foreach( $this->_getAttributeItems( $product->getAttributes() ) as $attrItem )
		{
			$prices = $attrItem->getRefItems( 'price', 'default' );

			if( count( $prices ) > 0 )
			{
				$attrPrice = $priceManager->getLowestPrice( $prices, $quantity );
				$price->addItem( $attrPrice );
			}
		}

		// remove product rebate of original price in favor to rebates granted for the order
		$price->setRebate( '0.00' );

		return $price;
	}


	/**
	 * Retrieves the product item specified by the given code.
	 *
	 * @param string $code Unique product code
	 * @return MShop_Product_Item_Interface Product item
	 * @throws Controller_Frontend_Basket_Exception
	 */
	protected function _getProductItem( $code )
	{
		$productManager = MShop_Factory::createManager( $this->_getContext(), 'product' );

		$search = $productManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', $code ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $productManager->searchItems( $search, array( 'price', 'text' ) );

		if( ( $productItem = reset( $result ) ) === false )
		{
			$msg = sprintf( 'No product with code "%1$s" found', $code );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		return $productItem;
	}


	/**
	 * Returns the list type item for the given domain and code.
	 *
	 * @param string $domain Domain name of the list type
	 * @param string $code Code of the list type
	 * @return MShop_Common_Item_Type_Interface List type item
	 */
	protected function _getProductListTypeItem( $domain, $code )
	{
		if( !isset( $this->_listTypeAttributes[$domain][$code] ) )
		{
			$listTypeManager = MShop_Factory::createManager( $this->_getContext(), 'product/list/type' );

			$listTypeSearch = $listTypeManager->createSearch( true );
			$expr = array (
				$listTypeSearch->compare( '==', 'product.list.type.domain', $domain ),
				$listTypeSearch->compare( '==', 'product.list.type.code', $code ),
				$listTypeSearch->getConditions(),
			);
			$listTypeSearch->setConditions( $listTypeSearch->combine( '&&', $expr ) );

			$listTypeItems = $listTypeManager->searchItems( $listTypeSearch );

			if( ( $listTypeItem = reset( $listTypeItems ) ) === false )
			{
				$msg = sprintf( 'List type for domain "%1$s" and code "%2$s" not found', $domain, $code );
				throw new Controller_Frontend_Basket_Exception( $msg );
			}

			$this->_listTypeAttributes[$domain][$code] = $listTypeItem;
		}

		return $this->_listTypeAttributes[$domain][$code];
	}


	/**
	 * Returns the product variants of a selection product that match the given attributes.
	 *
	 * @param MShop_Product_Item_Interface $productItem Product item including sub-products
	 * @param array $variantAttributeIds IDs for the variant-building attributes
	 * @param array $domains Names of the domain items that should be fetched too
	 * @return array List of products matching the given attributes
	 */
	protected function _getProductVariants( MShop_Product_Item_Interface $productItem, array $variantAttributeIds,
		array $domains = array( 'attribute', 'media', 'price', 'text' ) )
	{
		$subProductIds = array();
		foreach( $productItem->getRefItems( 'product', 'default', 'default' ) as $item ) {
			$subProductIds[] = $item->getId();
		}

		if( count( $subProductIds ) === 0 ) {
			return array();
		}

		$productManager = MShop_Factory::createManager( $this->_getContext(), 'product' );
		$search = $productManager->createSearch( true );

		$expr = array(
			$search->compare( '==', 'product.id', $subProductIds ),
			$search->getConditions(),
		);

		if( count( $variantAttributeIds ) > 0 )
		{
			$listTypeItem = $this->_getProductListTypeItem( 'attribute', 'variant' );

			$param = array( 'attribute', $listTypeItem->getId(), $variantAttributeIds );
			$cmpfunc = $search->createFunction( 'product.contains', $param );

			$expr[] = $search->compare( '==', $cmpfunc, count( $variantAttributeIds ) );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );

		return $productManager->searchItems( $search, $domains );
	}


	/**
	 * Returns the variant attributes and updates the price list if necessary.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $orderBaseProductItem Order product item
	 * @param MShop_Product_Item_Interface &$productItem Product item which is replaced if necessary
	 * @param array &$prices List of product prices that will be updated if necessary
	 * @param array $variantAttributeIds List of product variant attribute IDs
	 * @param array $options Associative list of options
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface[] List of order product attributes
	 * @throws Controller_Frontend_Basket_Exception If no product variant is found
	 */
	private function _getVariantDetails( MShop_Order_Item_Base_Product_Interface $orderBaseProductItem,
		MShop_Product_Item_Interface &$productItem, array &$prices, array $variantAttributeIds, array $options )
	{
		$attr = array();
		$productItems = $this->_getProductVariants( $productItem, $variantAttributeIds );

		if( count( $productItems ) > 1 )
		{
			$msg = sprintf( 'No unique article found for selected attributes and product ID "%1$s"', $productItem->getId() );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
		else if( ( $result = reset( $productItems ) ) !== false ) // count == 1
		{
			$productItem = $result;
			$orderBaseProductItem->setProductCode( $productItem->getCode() );

			$subprices = $productItem->getRefItems( 'price', 'default', 'default' );

			if( count( $subprices ) > 0 ) {
				$prices = $subprices;
			}

			$orderProductAttrManager = MShop_Factory::createManager( $this->_getContext(), 'order/base/product/attribute' );
			$variantAttributes = $productItem->getRefItems( 'attribute', null, 'variant' );

			foreach( $this->_getAttributes( array_keys( $variantAttributes ), array( 'text' ) ) as $attrItem )
			{
				$orderAttributeItem = $orderProductAttrManager->createItem();
				$orderAttributeItem->copyFrom( $attrItem );
				$orderAttributeItem->setType( 'variant' );

				$attr[] = $orderAttributeItem;
			}
		}
		else if( !isset( $options['variant'] ) || $options['variant'] != false ) // count == 0
		{
			$msg = sprintf( 'No article found for selected attributes and product ID "%1$s"', $productItem->getId() );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		return $attr;
	}


	/**
	 * Returns the value of an array or the default value if it's not available.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param string $name Name of the key to return the value for
	 * @param mixed $default Default value if no value is available for the given name
	 * @return mixed Value from the array or default value
	 */
	protected function _getValue( array $values, $name, $default = null )
	{
		if( isset( $values[$name] ) ) {
			return $values[$name];
		}

		return $default;
	}
}
