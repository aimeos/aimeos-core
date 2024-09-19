<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Product\Attribute;


/**
 * Default product attribute item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Product\Attribute\Iface
{
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return $this->get( 'order.product.attribute.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.siteid', $value );
	}


	/**
	 * Returns the original attribute ID of the product attribute item.
	 *
	 * @return string Attribute ID of the product attribute item
	 */
	public function getAttributeId() : string
	{
		return $this->get( 'order.product.attribute.attributeid', '' );
	}


	/**
	 * Sets the original attribute ID of the product attribute item.
	 *
	 * @param string|null $id Attribute ID of the product attribute item
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setAttributeId( ?string $id ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.attributeid', (string) $id );
	}


	/**
	 * Returns the ID of the ordered product as parent
	 *
	 * @return string|null ID of the ordered product
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.product.attribute.parentid' );
	}


	/**
	 * Sets the ID of the ordered product as parent
	 *
	 * @param string|null $id ID of the ordered product
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setParentId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.product.attribute.parentid', $id );
	}


	/**
	 * Returns the code of the product attibute.
	 *
	 * @return string Code of the attribute
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'order.product.attribute.code', '' );
	}


	/**
	 * Sets the code of the product attribute.
	 *
	 * @param string $code Code of the attribute
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.code', $this->checkCode( $code, 255 ) );
	}


	/**
	 * Returns the localized name of the product attribute.
	 *
	 * @return string Localized name of the product attribute
	 */
	public function getName() : string
	{
		return $this->get( 'order.product.attribute.name', '' );
	}


	/**
	 * Sets the localized name of the product attribute.
	 *
	 * @param string|null $name Localized name of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.name', (string) $name );
	}


	/**
	 * Returns the value of the product attribute.
	 *
	 * @return string|array Value of the product attribute
	 */
	public function getValue()
	{
		return $this->get( 'order.product.attribute.value', '' );
	}


	/**
	 * Sets the value of the product attribute.
	 *
	 * @param string|array $value Value of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setValue( $value ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.value', $value );
	}


	/**
	 * Returns the quantity of the product attribute.
	 *
	 * @return float Quantity of the product attribute
	 */
	public function getQuantity() : float
	{
		return $this->get( 'order.product.attribute.quantity', 1 );
	}


	/**
	 * Sets the quantity of the product attribute.
	 *
	 * @param float $value Quantity of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setQuantity( float $value ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.quantity', $value );
	}


	/**
	 * Returns the price of the product attribute.
	 *
	 * @return string|null Price of the product attribute
	 */
	public function getPrice() : ?string
	{
		return $this->get( 'order.product.attribute.price' );
	}


	/**
	 * Sets the price of the product attribute.
	 *
	 * @param string|null $value Price of the product attribute
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function setPrice( ?string $value ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		return $this->set( 'order.product.attribute.price', $value );
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order base product attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	{
		$this->setSiteId( $item->getSiteId() );
		$this->setAttributeId( $item->getId() );
		$this->setName( $item->getName() );
		$this->setCode( $item->getType() );
		$this->setValue( $item->getCode() );

		$this->setModified();

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface Order product attribute item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.product.attribute.attributeid': !$private ?: $item->setAttributeId( $value ); break;
				case 'order.product.attribute.parentid': !$private ?: $item->setParentId( $value ); break;
				case 'order.product.attribute.siteid': !$private ?: $item->setSiteId( $value ); break;
				case 'order.product.attribute.type': $item->setType( $value ); break;
				case 'order.product.attribute.code': $item->setCode( $value ); break;
				case 'order.product.attribute.name': $item->setName( $value ); break;
				case 'order.product.attribute.value': $item->setValue( $value ); break;
				case 'order.product.attribute.price': $item->setPrice( $value ); break;
				case 'order.product.attribute.quantity': $item->setQuantity( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['order.product.attribute.type'] = $this->getType();
		$list['order.product.attribute.code'] = $this->getCode();
		$list['order.product.attribute.name'] = $this->getName();
		$list['order.product.attribute.value'] = $this->getValue();
		$list['order.product.attribute.price'] = $this->getPrice();
		$list['order.product.attribute.quantity'] = $this->getQuantity();

		if( $private === true )
		{
			$list['order.product.attribute.parentid'] = $this->getParentId();
			$list['order.product.attribute.attributeid'] = $this->getAttributeId();
		}

		return $list;
	}
}
