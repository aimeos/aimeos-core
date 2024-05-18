<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Address\Standard
	implements \Aimeos\MShop\Customer\Item\Address\Iface
{
	/**
	 * Returns the type of the address item.
	 *
	 * @return string Address type
	 */
	public function getType() : string
	{
		return $this->get( 'customer.address.type', 'delivery' );
	}


	/**
	 * Sets the type of the address item.
	 *
	 * @param string $type Address type
	 * @return \Aimeos\MShop\Customer\Item\Address\Iface Address item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Customer\Item\Address\Iface
	{
		return $this->set( 'customer.address.type', $type );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Address item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $idx => $value )
		{
			$pos = strrpos( $idx, '.' );
			$key = $pos ? substr( $idx, $pos + 1 ) : $idx;

			switch( $key )
			{
				case 'type': $item = $item->setType( $value ); break;
				default: continue 2;
			}

			unset( $list[$idx] );
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

		$list[$this->getPrefix() . 'type'] = $this->getType();

		return $list;
	}
}
