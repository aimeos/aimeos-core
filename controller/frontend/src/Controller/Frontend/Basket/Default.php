<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Default.php 1116 2012-08-13 08:17:32Z nsendetzky $
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
	private $_basket = null;
	private $_listTypeAttributes = array();
	private $_domainManager;


	/**
	 * Initializes the frontend controller.
	 *
	 * @param MShop_Context_Item_Interface $context Object storing the required instances for manaing databases
	 *  connections, logger, session, etc.
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_domainManager = $this->_getDomainManager( 'order/base' );
		$this->_basket = $this->_domainManager->getSession();
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
	 * @param array $configAttributeIds  List of attribute IDs that doesn't identify a specific product in a
	 * 	selection of products but are stored together with the product (e.g. for configurable products)
	 * @param array $variantAttributeIds List of variant-building attribute IDs that identify a specific product
	 * 	in a selection products
	 * @param boolean $requireVariant True if a specific product must be matched by the variant-building attribute IDs
	 *  or false if the parent product can be added to the basket when the variant-building attributes don't match or
	 *  are missing
	 * @throws Controller_Frontend_Basket_Exception If the product isn't found
	 */
	public function addProduct( $prodid, $quantity = 1, $configAttributeIds = array(), $variantAttributeIds = array(), $requireVariant = true )
	{
		$this->_checkCategory( $prodid );
		$this->_checkStockLevel( $prodid, $quantity );


		$productManager = $this->_getDomainManager( 'product' );
		$productItem = $productManager->getItem( $prodid, array( 'media', 'price', 'product', 'text' ) );

		$orderBaseProductItem = $this->_getDomainManager( 'order/base/product' )->createItem();
		$orderBaseProductItem->copyFrom( $productItem );
		$orderBaseProductItem->setQuantity( $quantity );

		$prices = $productItem->getRefItems( 'price', 'default' );

		if( $productItem->getType() === 'select' )
		{
			$productItems = $this->_getProductVariants( $productItem, $variantAttributeIds );

			if( ( $productItem = reset( $productItems ) ) !== false )
			{
				$orderBaseProductItem->setProductCode( $productItem->getCode() );
				$orderBaseProductItem->setSupplierCode( $productItem->getSupplierCode() );
				$orderBaseProductItem->parentId = $orderBaseProductItem->getProductId();
				$orderBaseProductItem->setProductId( $productItem->getId() );

				$subprices = $productItem->getRefItems( 'price', 'default' );

				if( count( $subprices ) > 0 ) {
					$prices = $subprices;
				}
			}
			else if( $requireVariant === true )
			{
				$ids = join( ',', $variantAttributeIds );
				$msg = sprintf( 'No product found for ID "%1$s" and variant attribute IDs "%2$s"', $prodid, $ids );
				throw new Controller_Frontend_Basket_Exception( $msg );
			}
		}


		$orderAttributes = array();
		$orderProductAttributeManager = $this->_getDomainManager( 'order/base/product/attribute' );

		$priceManager = $this->_getDomainManager( 'price' );
		$price = $priceManager->getLowestPrice( $prices, $quantity );

		foreach( $this->_getAttributes( $configAttributeIds ) as $attrItem )
		{
			$prices = $attrItem->getRefItems( 'price', 'default' );

			if( count( $prices ) > 0 )
			{
				$attrPrice = $priceManager->getLowestPrice( $prices, $quantity );
				$price->addItem( $attrPrice );
			}

			$orderAttributeItem = $orderProductAttributeManager->createItem();
			$orderAttributeItem->copyFrom( $attrItem );
			$orderAttributeItem->setType( 'config' );

			$orderAttributes[] = $orderAttributeItem;
		}


		// remove product rebate of original price in favor to rebates granted for the order
		$price->setRebate( '0.00' );

		$orderBaseProductItem->setPrice( $price );
		$orderBaseProductItem->setAttributes( $orderAttributes );

		$this->_basket->addProduct( $orderBaseProductItem );
		$this->_domainManager->setSession( $this->_basket );
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
	 * @param array $configAttributeCodes Codes of the product config attributes that should be REMOVED
	 */
	public function editProduct( $position, $quantity, $configAttributeCodes = array() )
	{
		$product = $this->_basket->getProduct( $position );

		if( $product->getFlags() === MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE )
		{
			$msg = sprintf( 'Basket item at position "%1$d" cannot be changed', $position );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}


		$this->_checkStockLevel( $product->getProductId(), $quantity );

		$productManager = $this->_getDomainManager( 'product' );
		$productItem = $productManager->getItem( $product->getProductId(), array( 'price' ) );

		$prices = $productItem->getRefItems( 'price', 'default' );

		if( empty( $prices ) && isset( $product->parentId ) )
		{
			$productItem = $productManager->getItem( $product->parentId, array( 'price' ) );
			$prices = $productItem->getRefItems( 'price', 'default' );
		}

		$priceManager = $this->_getDomainManager( 'price' );
		$price = $priceManager->getLowestPrice( $prices, $quantity );


		$expr = array();
		$attributes = array();

		$attributeManager = $this->_getDomainManager( 'attribute' );
		$search = $attributeManager->createSearch( true );

		foreach( $product->getAttributes() as $item )
		{
			if( !in_array( $item->getCode(), $configAttributeCodes ) )
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

				$attributes[] = $item;
			}
		}

		if( !empty( $expr ) )
		{
			$search->setConditions( $search->combine( '||', $expr ) );
			$attributeItems = $attributeManager->searchItems( $search, array( 'price' ) );

			foreach( $attributeItems as $attrItem )
			{
				$prices = $attrItem->getRefItems( 'price', 'default' );

				if( count( $prices ) > 0 )
				{
					$attrPrice = $priceManager->getLowestPrice( $prices, $quantity );
					$price->addItem( $attrPrice );
				}
			}
		}

		// remove product rebate of original price in favor to rebates granted for the order
		$price->setRebate( '0.00' );

		$product->setPrice( $price );
		$product->setQuantity( $quantity );
		$product->setAttributes( $attributes );

		$this->_basket->deleteProduct( $position );
		$this->_basket->addProduct( $product, $position );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Sets the address of the customer in the basket.
	 *
	 * @param string $type Address type constant from MShop_Order_Item_Base_Address_Abstract
	 * @param MShop_Common_Item_Address_Interface|array|null $billing Address object or array with key/value pairs.
	 * 	In case of an array, the keys must be the same as the keys returned when calling toArray()
	 *  on the order base address object like "order.base.address.salutation"
	 * @throws Controller_Frontend_Basket_Exception If the billing or delivery address is not of any required type of
	 * 	if one of the keys is invalid when using an array with key/value pairs
	 */
	public function setAddress( $type, $value )
	{
		$orderAddressManager = $this->_getDomainManager( 'order/base/address' );
		$address = $orderAddressManager->createItem();
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
		$serviceManager = $this->_getDomainManager( 'service' );
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

		$orderBaseServiceManager = $this->_getDomainManager( 'order/base/service' );
		$orderServiceItem = $orderBaseServiceManager->createItem();
		$orderServiceItem->copyFrom( $serviceItem );

		$price = $provider->calcPrice( $this->_basket );
		// remove service rebate of original price
		$price->setRebate( '0.00' );
		$orderServiceItem->setPrice( $price );

		$orderBaseServiceAttributeManager = $orderBaseServiceManager->getSubManager('attribute');

		$attributeItems = array();
		foreach( $attributes as $key => $value )
		{
			$ordBaseAtrrItem = $orderBaseServiceAttributeManager->createItem();
			$ordBaseAtrrItem->setCode( $key );
			$ordBaseAtrrItem->setValue( strip_tags( $value ) ); // prevent XSS
			$ordBaseAtrrItem->setType( 'config' );

			$attributeItems[] = $ordBaseAtrrItem;
		}

		$orderServiceItem->setAttributes( $attributeItems );

		$this->_basket->setService( $orderServiceItem, $type );
		$this->_domainManager->setSession( $this->_basket );
	}


	/**
	 * Checks if the product is part of at least one category in the product catalog.
	 *
	 * @param string $prodid Unique ID of the product
	 * @throws Controller_Frontend_Basket_Exception If product is not associated to at least one category
	 */
	protected function _checkCategory( $prodid )
	{
		$catalogListManager = $this->_getDomainManager( 'catalog/list' );

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
	 * Checks if there are enough products in stock.
	 *
	 * @param string $prodid Unique ID of the product
	 * @param integer $quantity Number of products the customer would like to buy
	 * @throws Controller_Frontend_Basket_Exception If there are not enough products in stock
	 */
	protected function _checkStockLevel( $prodid, $quantity )
	{
		$manager = $this->_getDomainManager( 'product/stock' );

		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.stock.productid', $prodid ),
			$search->compare( '>=', 'product.stock.stocklevel', $quantity ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$result = $manager->searchItems( $search );

		if( reset( $result ) === false )
		{
			$msg = sprintf( 'There are not enough products (ID "%1$s") in stock', $prodid );
			throw new Controller_Frontend_Basket_Exception( $msg );
		}
	}


	/**
	 * Fills the order address object with the values from the array.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $address Address item to store the values into
	 * @param array $map Associative array of key/value pairs. The keys must be the same as when calling toArray() from
	 * 	an address item.
	 * @param string $prefix Key prefix like "customer." for a billing address or "customer.address." for a delivery
	 * 	address
	 * @throws Controller_Frontend_Basket_Exception
	 */
	protected function _setAddressFromArray( MShop_Order_Item_Base_Address_Interface $address, array $map )
	{
		$errors = array();

		foreach( $map as $key => $value )
		{
			try
			{
				$value = strip_tags( $value ); // prevent XSS

				switch( $key )
				{
					case 'order.base.address.salutation':
						$address->setSalutation( $value ); break;
					case 'order.base.address.company':
						$address->setCompany( $value ); break;
					case 'order.base.address.title':
						$address->setTitle( $value ); break;
					case 'order.base.address.firstname':
						$address->setFirstname( $value ); break;
					case 'order.base.address.lastname':
						$address->setLastName( $value ); break;
					case 'order.base.address.address1':
						$address->setAddress1( $value ); break;
					case 'order.base.address.address2':
						$address->setAddress2( $value ); break;
					case 'order.base.address.address3':
						$address->setAddress3( $value ); break;
					case 'order.base.address.postal':
						$address->setPostal( $value ); break;
					case 'order.base.address.city':
						$address->setCity( $value ); break;
					case 'order.base.address.state':
						$address->setState( $value ); break;
					case 'order.base.address.countryid':
						$address->setCountryId( $value ); break;
					case 'order.base.address.languageid':
						$address->setLanguageId( $value ); break;
					case 'order.base.address.telephone':
						$address->setTelephone( $value ); break;
					case 'order.base.address.email':
						$address->setEmail( $value ); break;
					case 'order.base.address.telefax':
						$address->setTelefax( $value ); break;
					case 'order.base.address.website':
						$address->setWebsite( $value ); break;
					case 'order.base.address.flag':
						$address->setFlag( $value ); break;
					default:
						$msg = sprintf( 'Invalid address property "%1$s" with value "%2$s"', $key, $value );
						throw new Controller_Frontend_Basket_Exception( $msg );
				}
			}
			catch( Exception $e )
			{
				$name = substr( $key, 19 );
				$errors[$name] = $e->getMessage();
			}
		}

		if( count( $errors ) > 0 )
		{
			$msg = sprintf( 'Invalid address properties, please check your input' );
			throw new Controller_Frontend_Basket_Exception( $msg, 0, null, $errors );
		}
	}


	/**
	 * Returns the attribute items for the given attribute IDs.
	 *
	 * @param array $attributeIds List of attribute IDs
	 * @param array $domains Names of the domain items that should be fetched too
	 * @return array List of items implementing MShop_Attribute_Item_Interface
	 * @throws Controller_Frontend_Basket_Exception If the actual attribute number doesn't match the expected one
	 */
	protected function _getAttributes( array $attributeIds, array $domains = array( 'price', 'text' ) )
	{
		if( empty( $attributeIds ) ) {
			return array();
		}

		$attributeManager = $this->_getDomainManager( 'attribute' );

		$search = $attributeManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'attribute.id', $attributeIds ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attrItems = $attributeManager->searchItems( $search, $domains );

		if( count( $attrItems ) !== count( $attributeIds ) )
		{
			$expected = join( ',', $attributeIds );
			$actual = join( ',', array_keys( $attrItems ) );
			$msg = sprintf( 'Available attribute IDs "%1$s" does not match the given attribute IDs "%2$s"', $actual, $expected );

			throw new Controller_Frontend_Basket_Exception( $msg );
		}

		return $attrItems;
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
			$listTypeManager = $this->_getDomainManager( 'product/list/type' );

			$listTypeSearch = $listTypeManager->createSearch( true );
			$expr = array (
				$listTypeSearch->compare( '==', 'product.list.type.domain', 'attribute' ),
				$listTypeSearch->compare( '==', 'product.list.type.code', 'default' ),
				$listTypeSearch->getConditions(),
			);
			$listTypeSearch->setConditions( $listTypeSearch->combine( '&&', $expr ) );

			$listTypeItems = $listTypeManager->searchItems( $listTypeSearch );

			if( ( $listTypeItem = reset( $listTypeItems ) ) === false )
			{
				$msg = sprintf( 'No list type for domain "%1$s" and code "%2$s" found', 'attribute', 'default' );
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
		array $domains = array( 'media', 'price', 'text' ) )
	{
		$subProductIds = array();
		foreach( $productItem->getRefItems( 'product', 'default' ) as $item ) {
			$subProductIds[] = $item->getId();
		}

		if( count( $subProductIds ) === 0 ) {
			return array();
		}

		$productManager = $this->_getDomainManager( 'product' );
		$search = $productManager->createSearch( true );

		$expr = array(
			$search->compare( '==', 'product.id', $subProductIds ),
			$search->getConditions(),
		);

		if( count( $variantAttributeIds ) > 0 )
		{
			$listTypeItem = $this->_getProductListTypeItem( 'attribute', 'default' );

			$param = array( 'attribute', $listTypeItem->getId(), $variantAttributeIds );
			$cmpfunc = $search->createFunction( 'product.contains', $param );

			$expr[] = $search->compare( '==', $cmpfunc, count( $variantAttributeIds ) );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );

		return $productManager->searchItems( $search, $domains );
	}
}
