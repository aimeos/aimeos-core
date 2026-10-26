<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Group\Item;


/**
 * Default group object
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Group\Item\Iface
{
	/**
	 * Returns the code of the group
	 *
	 * @return string Code of the group
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'group.code', '' );
	}


	/**
	 * Sets the new code of the group
	 *
	 * @param string $value Code of the group
	 * @return static Customer group item for chaining method calls
	 */
	public function setCode( string $value ) : static
	{
		return $this->set( 'group.code', $value );
	}


	/**
	 * Returns the label of the group
	 *
	 * @return string Label of the group
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'group.label', '' );
	}


	/**
	 * Sets the new label of the group
	 *
	 * @param string $value Label of the group
	 * @return static Customer group item for chaining method calls
	 */
	public function setLabel( string $value ) : static
	{
		return $this->set( 'group.label', $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Group item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'group.code': $item->setCode( (string) $value ); break;
				case 'group.label': $item->setLabel( (string) $value ); break;
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

		$list['group.code'] = $this->getCode();
		$list['group.label'] = $this->getLabel();

		return $list;
	}
}
