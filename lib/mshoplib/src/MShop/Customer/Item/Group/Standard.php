<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Group;


/**
 * Default customer group object
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Customer\Item\Group\Iface
{
	/**
	 * Initializes the customer group item
	 *
	 * @param array $values List of key/value pairs of the customer group
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'customer.group.', $values );
	}


	/**
	 * Returns the code of the customer group
	 *
	 * @return string Code of the customer group
	 */
	public function getCode() : string
	{
		return $this->get( 'customer.group.code', '' );
	}


	/**
	 * Sets the new code of the customer group
	 *
	 * @param string $value Code of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Customer\Item\Group\Iface
	{
		return $this->set( 'customer.group.code', $value );
	}


	/**
	 * Returns the label of the customer group
	 *
	 * @return string Label of the customer group
	 */
	public function getLabel() : string
	{
		return $this->get( 'customer.group.label', '' );
	}


	/**
	 * Sets the new label of the customer group
	 *
	 * @param string $value Label of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setLabel( string $value ) : \Aimeos\MShop\Customer\Item\Group\Iface
	{
		return $this->set( 'customer.group.label', $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'customer/group';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Group item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.group.code': $item = $item->setCode( $value ); break;
				case 'customer.group.label': $item = $item->setLabel( $value ); break;
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

		$list['customer.group.code'] = $this->getCode();
		$list['customer.group.label'] = $this->getLabel();

		return $list;
	}
}
