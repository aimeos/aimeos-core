<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Address;


/**
 * Default order address container object
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Address\Base
	implements \Aimeos\MShop\Order\Item\Address\Iface
{
	/**
	 * Initializes the objects with the given array of values.
	 *
	 * @param array $values List of address elements
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.address.', $values );
	}


	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return string|null Base ID
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.address.parentid' );
	}


	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param string|null $value New base ID
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
	 */
	public function setParentId( ?string $value ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		return $this->set( 'order.address.parentid', $value );
	}


	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId() : string
	{
		return $this->get( 'order.address.addressid', '' );
	}


	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
	 */
	public function setAddressId( string $addrid ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		return $this->set( 'order.address.addressid', $addrid );
	}


	/**
	 * Returns the position of the address in the order.
	 *
	 * @return integer|null Address position in the order from 0-n
	 */
	public function getPosition() : ?int
	{
		if( ( $result = $this->get( 'order.address.position' ) ) !== null ) {
			return $result;
		}

		return null;
	}


	/**
	 * Sets the position of the address within the list of ordered addresses
	 *
	 * @param int|null $value Address position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		if( $value < 0 ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Order address position "%1$s" must be greater than 0', $value ) );
		}

		return $this->set( 'order.address.position', $value );
	}


	/**
	 * Returns the address type which can be billing or delivery.
	 *
	 * @return string Address type
	 */
	public function getType() : string
	{
		return $this->get( 'order.address.type', \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT );
	}


	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.address.type', $this->checkType( $type ) );
	}


	/**
	 * Copys all data from a given address item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item New address
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order base address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $item ) : \Aimeos\MShop\Common\Item\Address\Iface
	{
		parent::copyFrom( $item );

		$this->setAddressId( (string) $item->getId() );
		$this->setModified();

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order address item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.address.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.address.addressid': $item = $item->setAddressId( $value ); break;
				case 'order.address.position': $item = $item->setPosition( $value ); break;
				case 'order.address.type': $item = $item->setType( $value ); break;
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

		$list['order.address.type'] = $this->getType();
		$list['order.address.position'] = $this->getPosition();
		$list['order.address.addressid'] = $this->getAddressId();

		if( $private === true ) {
			$list['order.address.parentid'] = $this->getParentId();
		}

		return $list;
	}

}
