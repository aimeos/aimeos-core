<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Address\Base
	implements \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\Position\Iface
{
	private $prefix;


	/**
	 * Initializes the provider common address item object
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values List of attributes that belong to the provider common address item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = $prefix;
	}


	/**
	 * Returns the customer ID this address belongs to
	 *
	 * @return string|null Customer ID of the address
	 */
	public function getParentId() : ?string
	{
		return $this->get( $this->prefix . 'parentid' );
	}


	/**
	 * Sets the new customer ID this address belongs to
	 *
	 * @param string|null $parentid New customer ID of the address
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'parentid', $parentid );
	}


	/**
	 * Returns the position of the address item.
	 *
	 * @return int Position of the address item
	 */
	public function getPosition() : int
	{
		return $this->get( $this->prefix . 'position', 0 );
	}


	/**
	 * Sets the Position of the address item.
	 *
	 * @param int $position Position of the address item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setPosition( int $position ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'position', $position );
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

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case $this->prefix . 'position': $item = $item->setPosition( $value ); break;
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

		$list[$this->prefix . 'position'] = $this->getPosition();

		if( $private === true ) {
			$list[$this->prefix . 'parentid'] = $this->getParentId();
		}

		return $list;
	}

}
