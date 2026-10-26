<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Service\Attribute;


/**
 * Default order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Service\Attribute\Iface
{
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return (string) $this->get( 'order.service.attribute.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : static
	{
		return $this->set( 'order.service.attribute.siteid', $value );
	}


	/**
	 * Returns the original attribute ID of the service attribute item.
	 *
	 * @return string Attribute ID of the service attribute item
	 */
	public function getAttributeId() : string
	{
		return (string) $this->get( 'order.service.attribute.attributeid', '' );
	}


	/**
	 * Sets the original attribute ID of the service attribute item.
	 *
	 * @param string|null $id Attribute ID of the service attribute item
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setAttributeId( ?string $id ) : static
	{
		return $this->set( 'order.service.attribute.attributeid', (string) $id );
	}


	/**
	 * Returns the ID of the ordered service item as parent
	 *
	 * @return string|null ID of the ordered service item
	 */
	public function getParentId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'order.service.attribute.parentid' );
	}


	/**
	 * Sets the ID of the ordered service item as parent
	 *
	 * @param string|null $id ID of the ordered service item
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setParentId( ?string $id ) : static
	{
		return $this->set( 'order.service.attribute.parentid', $id );
	}


	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string Code of the service attribute item
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'order.service.attribute.code', '' );
	}


	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setCode( string $code ) : static
	{
		return $this->set( 'order.service.attribute.code', \Aimeos\Utils::code( $code, 255 ) );
	}


	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName() : string
	{
		return (string) $this->get( 'order.service.attribute.name', '' );
	}


	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string|null $name Name as defined by the service provider
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setName( ?string $name ) : static
	{
		return $this->set( 'order.service.attribute.name', (string) $name );
	}


	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return string|array Service attribute item value
	 */
	public function getValue()
	{
		// @phpstan-ignore return.type
		return $this->get( 'order.service.attribute.value', '' );
	}


	/**
	 * Sets a new value for the service item.
	 *
	 * @param string|array $value service attribute item value
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setValue( $value ) : static
	{
		return $this->set( 'order.service.attribute.value', $value );
	}


	/**
	 * Returns the quantity of the service attribute.
	 *
	 * @return float Quantity of the service attribute
	 */
	public function getQuantity() : float
	{
		return (float) $this->get( 'order.service.attribute.quantity', 1 );
	}


	/**
	 * Sets the quantity of the service attribute.
	 *
	 * @param float $value Quantity of the service attribute
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setQuantity( float $value ) : static
	{
		return $this->set( 'order.service.attribute.quantity', $value );
	}


	/**
	 * Returns the price of the service attribute.
	 *
	 * @return string|null Price of the service attribute
	 */
	public function getPrice() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'order.service.attribute.price' );
	}


	/**
	 * Sets the price of the service attribute.
	 *
	 * @param string|null $value Price of the service attribute
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function setPrice( ?string $value ) : static
	{
		return $this->set( 'order.service.attribute.price', $value );
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return static Order base service attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item ) : static
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
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Order service attribute item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.service.attribute.attributeid': !$private ?: $item->setAttributeId( $value ? (string) $value : null ); break;
				case 'order.service.attribute.parentid': !$private ?: $item->setParentId( $value ? (string) $value : null ); break;
				case 'order.service.attribute.siteid': !$private ?: $item->setSiteId( (string) $value ); break;
				case 'order.service.attribute.type': $item->setType( (string) $value ); break;
				case 'order.service.attribute.name': $item->setName( $value ? (string) $value : null ); break;
				case 'order.service.attribute.code': $item->setCode( (string) $value ); break;
				case 'order.service.attribute.value': $item->setValue( is_array( $value ) ? $value : (string) $value ); break;
				case 'order.service.attribute.price': $item->setPrice( $value ? (string) $value : null ); break;
				case 'order.service.attribute.quantity': $item->setQuantity( (float) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['order.service.attribute.type'] = $this->getType();
		$list['order.service.attribute.name'] = $this->getName();
		$list['order.service.attribute.code'] = $this->getCode();
		$list['order.service.attribute.value'] = $this->getValue();
		$list['order.service.attribute.price'] = $this->getPrice();
		$list['order.service.attribute.quantity'] = $this->getQuantity();

		if( $private === true )
		{
			$list['order.service.attribute.parentid'] = $this->getParentId();
			$list['order.service.attribute.attributeid'] = $this->getAttributeId();
		}

		return $list;
	}
}
