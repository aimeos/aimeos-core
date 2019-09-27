<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	public function __construct( $values = [] )
	{
		parent::__construct( 'customer.group.', $values );
	}


	/**
	 * Returns the code of the customer group
	 *
	 * @return string Code of the customer group
	 */
	public function getCode()
	{
		return (string) $this->get( 'customer.group.code', '' );
	}


	/**
	 * Sets the new code of the customer group
	 *
	 * @param string $value Code of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setCode( $value )
	{
		return $this->set( 'customer.group.code', (string) $value );
	}


	/**
	 * Returns the label of the customer group
	 *
	 * @return string Label of the customer group
	 */
	public function getLabel()
	{
		return (string) $this->get( 'customer.group.label', '' );
	}


	/**
	 * Sets the new label of the customer group
	 *
	 * @param string $value Label of the customer group
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Customer group item for chaining method calls
	 */
	public function setLabel( $value )
	{
		return $this->set( 'customer.group.label', (string) $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'customer/group';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Group item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
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
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['customer.group.code'] = $this->getCode();
		$list['customer.group.label'] = $this->getLabel();

		return $list;
	}
}
