<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Product;


/**
 * Product item of order base
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base\Product\Base
	implements \Aimeos\MShop\Order\Item\Base\Product\Iface
{
	private $price;
	private $attributes;
	private $attributesMap;
	private $products;
	private $values;

	/**
	 * Initializes the order product instance.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param array $values Associative list of order product values
	 * @param array $attributes Attributes to be set on initialisation
	 */

	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = array(), array $attributes = array(), array $products = array() )
	{
		parent::__construct( 'order.base.product.', $values );

		$this->price = $price;
		$this->values = $values;

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Attribute\\Iface', $attributes );
		$this->attributes = $attributes;

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $products );
		$this->products = $products;
	}

	/**
	 * Clones internal objects of the order base product item.
	 */
	public function __clone()
	{
		$this->price = clone $this->price;
	}

	/**
	 * Returns the base ID.
	 *
	 * @return integer Base ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->values['baseid'] ) ? (int) $this->values['baseid'] : null );
	}


	/**
	 * Sets the base order ID the product belongs to.
	 *
	 * @param integer $value New order base ID
	 */
	public function setBaseId( $value )
	{
		if( $value == $this->getBaseId() ) { return; }

		$this->values['baseid'] = ( $value !== null ? (int) $value : null );
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
		return ( isset( $this->values['ordprodid'] ) ? (int) $this->values['ordprodid'] : null );
	}


	/**
	 * Sets the parent ID of the ordered product.
	 * This ID relates to another product of the same order and provides a relation for e.g. sub-products in bundles.
	 *
	 * @param integer|null $orderProductId Order product ID
	 */
	public function setOrderProductId( $orderProductId )
	{
		if( $orderProductId === $this->getOrderProductId() ) { return; }

		$this->values['ordprodid'] = ( $orderProductId !== null ? (int) $orderProductId : null );
		$this->setModified();
	}


	/**
	 * Returns the type of the ordered product.
	 *
	 * @return string Type of the ordered product
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : null );
	}

	/**
	 * Sets the type of the ordered product.
	 *
	 * @param string Type of the order product
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return; }

		$this->values['type'] = (string) $type;
		$this->setModified();
	}

	/**
	 * Returns a array of order base product items
	 *
	 * @return array Associative list of product items that implements \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	public function getProducts()
	{
		return $this->products;
	}

	/**
	 * Sets a array of order base product items
	 *
	 * @param array Associative list of product items which must implement the \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	public function setProducts( array $products )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $products );
		$this->products = $products;
		$this->setModified();
	}


	/**
	 * Returns the supplier code.
	 *
	 * @return string the code of supplier
	 */
	public function getSupplierCode()
	{
		return ( isset( $this->values['suppliercode'] ) ? (string) $this->values['suppliercode'] : '' );
	}


	/**
	 * Sets the supplier code.
	 *
	 * @param string $suppliercode Code of supplier
	 */
	public function setSupplierCode( $suppliercode )
	{
		$this->checkCode( $suppliercode );

		if( $suppliercode == $this->getSupplierCode() ) { return; }

		$this->values['suppliercode'] = (string) $suppliercode;
		$this->setModified();
	}


	/**
	 * Returns the product ID the customer has selected.
	 *
	 * @return string Original product ID
	 */
	public function getProductId()
	{
		return ( isset( $this->values['prodid'] ) ? (string) $this->values['prodid'] : '' );
	}


	/**
	 * Sets the ID of a product the customer has selected.
	 *
	 * @param string Product Code ID
	 */
	public function setProductId( $id )
	{
		if( $id == $this->getProductId() ) { return; }

		$this->values['prodid'] = (string) $id;
		$this->setModified();
	}


	/**
	 * Returns the product code the customer has selected.
	 *
	 * @return string Product code
	 */
	public function getProductCode()
	{
		return ( isset( $this->values['prodcode'] ) ? (string) $this->values['prodcode'] : '' );
	}


	/**
	 * Sets the code of a product the customer has selected.
	 *
	 * @param string $code Product code
	 */
	public function setProductCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getProductCode() ) { return; }

		$this->values['prodcode'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the code of the warehouse the product should be retrieved from.
	 *
	 * @return string Warehouse code
	 */
	public function getWarehouseCode()
	{
		return ( isset( $this->values['warehousecode'] ) ? (string) $this->values['warehousecode'] : '' );
	}


	/**
	 * Sets the code of the warehouse the product should be retrieved from.
	 *
	 * @param string $code Warehouse code
	 */
	public function setWarehouseCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getWarehouseCode() ) { return; }

		$this->values['warehousecode'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the localized name of the product.
	 *
	 * @return string Returns the localized name of the product
	 */
	public function getName()
	{
		return ( isset( $this->values['name'] ) ? (string) $this->values['name'] : '' );
	}


	/**
	 * Sets the localized name of the product.
	 *
	 * @param string $value Localized name of the product
	 */
	public function setName( $value )
	{
		if( $value == $this->getName() ) { return; }

		$this->values['name'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		return ( isset( $this->values['mediaurl'] ) ? (string) $this->values['mediaurl'] : '' );
	}


	/**
	 * Sets the media url of the product the customer has added.
	 *
	 * @param string $value Location of the media/picture
	 */
	public function setMediaUrl( $value )
	{
		if( $value == $this->getMediaUrl() ) { return; }

		$this->values['mediaurl'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the amount of products the customer has added.
	 *
	 * @return integer Amount of products
	 */
	public function getQuantity()
	{
		return ( isset( $this->values['quantity'] ) ? (int) $this->values['quantity'] : 1 );
	}


	/**
	 * Sets the amount of products the customer has added.
	 *
	 * @param integer $quantity Amount of products
	 */
	public function setQuantity( $quantity )
	{
		if( !is_numeric( $quantity ) ) {
			throw new \Aimeos\MShop\Order\Exception( 'Quantity is invalid. Please enter a positive integer' );
		}

		$quantity = (int) $quantity;

		if( $quantity == $this->getQuantity() ) { return; }

		if( $quantity < 1 || $quantity > 2147483647 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Quantity must be a positive integer and must not exceed %1$d', 2147483647 ) );
		}

		$this->values['quantity'] = $quantity;
		$this->setModified();
	}


	/**
	 * Returns the price item for the product.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
	 */
	public function getPrice()
	{
		return $this->price;
	}


	/**
	 * Sets the price item for the product.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price )
	{
		if( $price === $this->price ) { return; }

		$this->price = $price;
		$this->setModified();
	}


	/**
	 * Returns the price item for the product whose values are multiplied with the quantity.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, additional costs and rebate
	 */
	public function getSumPrice()
	{
		$price = clone $this->price;

		$price->setValue( $price->getValue() * $this->values['quantity'] );
		$price->setCosts( $price->getCosts() * $this->values['quantity'] );
		$price->setRebate( $price->getRebate() * $this->values['quantity'] );

		return $price;
	}


	/**
	 * 	Returns the flags for the product item.
	 *
	 * @return integer Flags, e.g. for immutable products
	 */
	public function getFlags()
	{
		return ( isset( $this->values['flags'] ) ? (int) $this->values['flags'] : \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE );
	}


	/**
	 * Sets the new value for the product item flags.
	 *
	 * @param integer $value Flags, e.g. for immutable products
	 */
	public function setFlags( $value )
	{
		if( $value == $this->getFlags() ) { return; }

		$this->checkFlags( $value );
		$this->values['flags'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the position of the product in the order.
	 *
	 * @return integer|null Product position in the order from 1-n
	 */
	public function getPosition()
	{
		return ( isset( $this->values['pos'] ) ? (int) $this->values['pos'] : null );
	}


	/**
	 * Sets the position of the product within the list of ordered products.
	 *
	 * @param integer|null $value Product position in the order from 1-n or null for resetting the position
	 * @throws \Aimeos\MShop\Order\Exception If there's already a position set
	 */
	public function setPosition( $value )
	{
		if( $value == $this->getPosition() ) { return; }

		if( $value !== null && $value < 1 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order product position "%1$s" must be greater than 0', $value ) );
		}

		$this->values['pos'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the current delivery status of the order product item.
	 * The returned status values are the STAT_* constants from the
	 * \Aimeos\MShop\Order\Item\Base class
	 *
	 * @return integer Delivery status of the product
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED );
	}


	/**
	 * Sets the new delivery status of the order product item.
	 * Possible status values are the STAT_* constants from the
	 * \Aimeos\MShop\Order\Item\Base class
	 *
	 * @param integer $value New delivery status of the product
	 */
	public function setStatus( $value )
	{
		$this->values['status'] = (int) $value;
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
		$map = $this->getAttributeMap();

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
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( $code, $type = '' )
	{
		$map = $this->getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code];
		}

		return null;
	}


	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function getAttributes( $type = null )
	{
		if( $type === null ) {
			return $this->attributes;
		}

		$list = array();

		foreach( $this->attributes as $attrItem )
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
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Service attribute item
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item )
	{
		$this->getAttributeMap();

		$type = $item->getType();
		$code = $item->getCode();

		if( !isset( $this->attributesMap[$type][$code] ) )
		{
			$this->attributesMap[$type][$code] = $item;
			$this->attributes[] = $item;
		}

		$this->attributesMap[$type][$code]->setValue( $item->getValue() );
		$this->setModified();
	}


	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param array $attributes List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function setAttributes( array $attributes )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Attribute\\Iface', $attributes );

		$this->attributes = $attributes;
		$this->attributesMap = null;
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
				case 'order.base.product.price': $this->price->setValue( $value ); break;
				case 'order.base.product.costs': $this->price->setCosts( $value ); break;
				case 'order.base.product.rebate': $this->price->setRebate( $value ); break;
				case 'order.base.product.taxrate': $this->price->setTaxRate( $value ); break;
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
		$list['order.base.product.price'] = $this->price->getValue();
		$list['order.base.product.costs'] = $this->price->getCosts();
		$list['order.base.product.rebate'] = $this->price->getRebate();
		$list['order.base.product.taxrate'] = $this->price->getTaxRate();
		$list['order.base.product.quantity'] = $this->getQuantity();
		$list['order.base.product.status'] = $this->getStatus();
		$list['order.base.product.flags'] = $this->getFlags();

		return $list;
	}

	/**
	 * Compares the properties of the given order product item with its own ones.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item Order product item
	 * @return boolean True if the item properties are equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Order\Item\Base\Product\Iface $item )
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
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item to copy from
	 */
	public function copyFrom( \Aimeos\MShop\Product\Item\Iface $product )
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
	 * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface as value
	 */
	protected function getAttributeMap()
	{
		if( !isset( $this->attributesMap ) )
		{
			$this->attributesMap = array();

			foreach( $this->attributes as $attribute ) {
				$this->attributesMap[$attribute->getType()][$attribute->getCode()] = $attribute;
			}
		}

		return $this->attributesMap;
	}
}
