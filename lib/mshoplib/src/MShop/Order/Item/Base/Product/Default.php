<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Product item of order base
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Product_Default
	extends MShop_Order_Item_Base_Product_Abstract
	implements MShop_Order_Item_Base_Product_Interface
{
	private $_price;
	private $_attributes;
	private $_attributesMap;
	private $_products;
	private $_values;

	/**
	 * Initializes the order product instance.
	 *
	 * @param MShop_Price_Item_Interface $price Price item
	 * @param array $values Associative list of order product values
	 * @param array $attributes Attributes to be set on initialisation
	 */

	public function __construct( MShop_Price_Item_Interface $price, array $values = array(), array $attributes = array(), array $products = array() )
	{
		parent::__construct( 'order.base.product.', $values );

		$this->_price = $price;
		$this->_values = $values;

		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Attribute_Interface', $attributes );
		$this->_attributes = $attributes;

		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Interface', $products );
		$this->_products = $products;
	}

	/**
	 * Clones internal objects of the order base product item.
	 */
	public function __clone()
	{
		$this->_price = clone $this->_price;
	}

	/**
	 * Returns the base ID.
	 *
	 * @return integer Base ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param integer $value New order base ID
	 */
	public function setBaseId( $value )
	{
		if ( $value == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the parent ID of the ordered product if there is one.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @return integer|null Order product ID
	 */
	public function getOrderProductId()
	{
		return ( isset( $this->_values['ordprodid'] ) ? (int) $this->_values['ordprodid'] : null );
	}


	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param integer|null $orderProductId Order product ID
	 */
	public function setOrderProductId( $orderProductId )
	{
		if ( $orderProductId === $this->getOrderProductId() ) { return; }

		$this->_values['ordprodid'] = ( $orderProductId !== null ? (int) $orderProductId : null );
		$this->setModified();
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}

	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string Type of the order product
	 */
	public function setType( $type )
	{
		if ( $type == $this->getType() ) { return; }

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}

	/**
	 * Returns a array of order base product items
	 *
	 * @return array Associative list of product items that implements MShop_Order_Item_Base_Product_Interface
	 */
	public function getProducts()
	{
		return $this->_products;
	}

	/**
	 * Sets a array of order base product items
	 *
	 * @param array Associative list of product items which must implement the MShop_Order_Item_Base_Product_Interface
	 */
	public function setProducts( array $products )
	{
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Interface', $products );
		$this->_products = $products;
		$this->setModified();
	}


	/**
	 * Returns the supplier code.
	 *
	 * @return string the code of supplier
	 */
	public function getSupplierCode()
	{
		return ( isset( $this->_values['suppliercode'] ) ? (string) $this->_values['suppliercode'] : '' );
	}


	/**
	 * Sets the supplier code.
	 *
	 * @param string $suppliercode Code of supplier
	 */
	public function setSupplierCode( $suppliercode )
	{
		$this->_checkCode( $suppliercode );

		if ( $suppliercode == $this->getSupplierCode() ) { return; }

		$this->_values['suppliercode'] = (string) $suppliercode;
		$this->setModified();
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId()
	{
		return ( isset( $this->_values['prodid'] ) ? (string) $this->_values['prodid'] : '' );
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string Product Code ID
	 */
	public function setProductId( $id )
	{
		if ( $id == $this->getProductId() ) { return; }

		$this->_values['prodid'] = (string) $id;
		$this->setModified();
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode()
	{
		return ( isset( $this->_values['prodcode'] ) ? (string) $this->_values['prodcode'] : '' );
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 */
	public function setProductCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getProductCode() ) { return; }

		$this->_values['prodcode'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the code of the warehouse the product should be retrieved from.
	 *
	 * @return string Warehouse code
	 */
	public function getWarehouseCode()
	{
		return ( isset( $this->_values['warehousecode'] ) ? (string) $this->_values['warehousecode'] : '' );
	}


	/**
	 * Sets the code of the warehouse the product should be retrieved from.
	 *
	 * @param string $code Warehouse code
	 */
	public function setWarehouseCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getWarehouseCode() ) { return; }

		$this->_values['warehousecode'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName()
	{
		return ( isset( $this->_values['name'] ) ? (string) $this->_values['name'] : '' );
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string $value Localized name of the product
	 */
	public function setName( $value )
	{
		if ( $value == $this->getName() ) { return; }

		$this->_values['name'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		return ( isset( $this->_values['mediaurl'] ) ? (string) $this->_values['mediaurl'] : '' );
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string $value Location of the media/picture
	 */
	public function setMediaUrl( $value )
	{
		if ( $value == $this->getMediaUrl() ) { return; }

		$this->_values['mediaurl'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return integer Amount of products
	 */
	public function getQuantity()
	{
		return ( isset( $this->_values['quantity'] ) ? (int) $this->_values['quantity'] : 1 );
	}


	/**
	 * Sets the amount of products the customer has added.
	 *
	 * @param integer $quantity Amount of products
	 */
	public function setQuantity( $quantity )
	{
		if( !is_numeric( $quantity ) ) {
			throw new MShop_Order_Exception( 'Quantity is invalid. Please enter a positive integer' );
		}

		$quantity = (int) $quantity;

		if( $quantity == $this->getQuantity() ) { return; }

		if( $quantity < 1 || $quantity > 2147483647 ) {
			throw new MShop_Order_Exception( sprintf( 'Quantity must be a positive integer and must not exceed %1$d', 2147483647 ) );
		}

		$this->_values['quantity'] = $quantity;
		$this->setModified();
	}


	/**
	 * Returns the price item for the product.
	 *
	 * @return MShop_Price_Item_Interface Price item with price, costs and rebate
	 */
	public function getPrice()
	{
		return $this->_price;
	}


	/**
	 * Sets the price item for the product.
	 *
	 * @param MShop_Price_Item_Interface $price Price item containing price and additional costs
	 */
	public function setPrice( MShop_Price_Item_Interface $price )
	{
		if ( $price === $this->_price ) { return; }

		$this->_price = $price;
		$this->setModified();
	}


	/**
	 * Returns the price item for the product whose values are multiplied with the quantity.
	 *
	 * @return MShop_Price_Item_Interface Price item with price, additional costs and rebate
	 */
	public function getSumPrice()
	{
		$price = clone $this->_price;

		$price->setValue( $price->getValue() * $this->_values['quantity'] );
		$price->setCosts( $price->getCosts() * $this->_values['quantity'] );
		$price->setRebate( $price->getRebate() * $this->_values['quantity'] );

		return $price;
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return integer Flags, e.g. for immutable products
	 */
	public function getFlags()
	{
		return ( isset( $this->_values['flags'] ) ? (int) $this->_values['flags'] : MShop_Order_Item_Base_Product_Abstract::FLAG_NONE );
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param integer $value Flags, e.g. for immutable products
	 */
	public function setFlags( $value )
	{
		if ( $value == $this->getFlags() ) { return; }

		$this->_checkFlags($value);
		$this->_values['flags'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return integer|null Product position in the order from 1-n
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : null );
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param integer|null $value Product position in the order from 1-n or null for resetting the position
	 * @throws MShop_Order_Exception If there's already a position set
	 */
	public function setPosition( $value )
	{
		if( $value == $this->getPosition() ) { return; }

		if( $value !== null && $value < 1 ) {
			throw new MShop_Order_Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		$this->_values['pos'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the current delivery status of the order product item.
	 * The returned status values are the STAT_* constants from the
	 * MShop_Order_Item_Abstract class
	 *
	 * @return integer Delivery status of the product
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : MShop_Order_Item_Abstract::STAT_UNFINISHED );
	}


	/**
	 * Sets the new delivery status of the order product item.
	 * Possible status values are the STAT_* constants from the
	 * MShop_Order_Item_Abstract class
	 *
	 * @param integer $value New delivery status of the product
	 */
	public function setStatus( $value )
	{
		$this->_values['status'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the value of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return string|null value of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( $code, $type = '' )
	{
		$map = $this->_getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code]->getValue();
		}

		return null;
	}


	/**
	 * Returns the attribute item for the ordered product with the given code.
	 *
	 * @param string $code code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( $code, $type = '' )
	{
		$map = $this->_getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code];
		}

		return null;
	}


	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function getAttributes( $type = null )
	{
		if( $type === null ) {
			return $this->_attributes;
		}

		$list = array();

		foreach( $this->_attributes as $attrItem )
		{
			if( $attrItem->getType() === $type ) {
				$list[] = $attrItem;
			}
		}

		return $list;
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param MShop_Order_Item_Base_Product_Attribute_Interface $item Service attribute item
	 */
	public function setAttributeItem( MShop_Order_Item_Base_Product_Attribute_Interface $item )
	{
		$this->_getAttributeMap();

		$type = $item->getType();
		$code = $item->getCode();

		if( !isset( $this->_attributesMap[$type][$code] ) )
		{
			$this->_attributesMap[$type][$code] = $item;
			$this->_attributes[] = $item;
		}

		$this->_attributesMap[$type][$code]->setValue( $item->getValue() );
		$this->setModified();
	}


	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param array $attributes List of attribute items implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function setAttributes( array $attributes )
	{
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Product_Attribute_Interface', $attributes );

		$this->_attributes = $attributes;
		$this->_attributesMap = null;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.product.baseid': $this->setBaseId( $value ); break;
				case 'order.base.product.ordprodid': $this->setOrderProductId( $value ); break;
				case 'order.base.product.type': $this->setType( $value ); break;
				case 'order.base.product.suppliercode': $this->setSupplierCode( $value ); break;
				case 'order.base.product.productid': $this->setProductId( $value ); break;
				case 'order.base.product.prodcode': $this->setProductCode( $value ); break;
				case 'order.base.product.name': $this->setName( $value ); break;
				case 'order.base.product.mediaurl': $this->setMediaUrl( $value ); break;
				case 'order.base.product.position': $this->setPosition( $value ); break;
				case 'order.base.product.quantity': $this->setQuantity( $value ); break;
				case 'order.base.product.status': $this->setStatus( $value ); break;
				case 'order.base.product.flags': $this->setFlags( $value ); break;
				case 'order.base.product.price': $this->_price->setValue( $value ); break;
				case 'order.base.product.costs': $this->_price->setCosts( $value ); break;
				case 'order.base.product.rebate': $this->_price->setRebate( $value ); break;
				case 'order.base.product.taxrate': $this->_price->setTaxRate( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['order.base.product.baseid'] = $this->getBaseId();
		$list['order.base.product.ordprodid'] = $this->getOrderProductId();
		$list['order.base.product.type'] = $this->getType();
		$list['order.base.product.suppliercode'] = $this->getSupplierCode();
		$list['order.base.product.productid'] = $this->getProductId();
		$list['order.base.product.prodcode'] = $this->getProductCode();
		$list['order.base.product.name'] = $this->getName();
		$list['order.base.product.mediaurl'] = $this->getMediaUrl();
		$list['order.base.product.position'] = $this->getPosition();
		$list['order.base.product.price'] = $this->_price->getValue();
		$list['order.base.product.costs'] = $this->_price->getCosts();
		$list['order.base.product.rebate'] = $this->_price->getRebate();
		$list['order.base.product.taxrate'] = $this->_price->getTaxRate();
		$list['order.base.product.quantity'] = $this->getQuantity();
		$list['order.base.product.status'] = $this->getStatus();
		$list['order.base.product.flags'] = $this->getFlags();

		return $list;
	}

	/**
	 * Compares the properties of the given order product item with its own ones.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $item Order product item
	 * @return boolean True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( MShop_Order_Item_Base_Product_Interface $item )
	{
		if( $this->getFlags() === $item->getFlags()
			&& $this->getName() === $item->getName()
			&& $this->getProductCode() === $item->getProductCode()
			&& $this->getSupplierCode() === $item->getSupplierCode()
			&& $this->getPrice()->compare( $item->getPrice() ) === true
		) {
			return true;
		}

		return false;
	}


	/**
	 * Copys all data from a given product item.
	 *
	 * @param MShop_Product_Item_Interface $product Product item to copy from
	 */
	public function copyFrom( MShop_Product_Item_Interface $product )
	{
		$this->setName( $product->getName() );
		$this->setType( $product->getType() );
		$this->setSupplierCode( $product->getSupplierCode() );
		$this->setProductCode( $product->getCode() );
		$this->setProductId( $product->getId() );

		$items = $product->getRefItems( 'media', 'default', 'default' );
		if( ( $item = reset( $items ) ) !== false ) {
			$this->setMediaUrl( $item->getPreview() );
		}

		$this->setModified();
	}


	/**
	 * Returns the attribute map for the ordered products.
	 *
	 * @return array Associative list of type and code as key and an MShop_Order_Item_Base_Product_Attribute_Interface as value
	 */
	protected function _getAttributeMap()
	{
		if( !isset( $this->_attributesMap ) )
		{
			$this->_attributesMap = array();

			foreach( $this->_attributes as $attribute ) {
				$this->_attributesMap[ $attribute->getType() ][ $attribute->getCode() ] = $attribute;
			}
		}

		return $this->_attributesMap;
	}
}
