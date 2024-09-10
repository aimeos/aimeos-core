<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\AddressRef;


/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Supplier\Item\Iface
{
	use ListsRef\Traits, AddressRef\Traits  {
		ListsRef\Traits::__clone insteadof AddressRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		AddressRef\Traits::__clone as __cloneAddress;
	}


	/**
	 * Initializes the supplier item object
	 *
	 * @param string $prefix Domain prefix
	 * @param array $values List of attributes that belong to the supplier item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->initListItems( $values['.listitems'] ?? [] );
		$this->initAddressItems( $values['.addritems'] ?? [] );
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();
		$this->__cloneList();
		$this->__cloneAddress();
	}


	/**
	 * Returns the label of the supplier item.
	 *
	 * @return string label of the supplier item
	 */
	public function getLabel() : string
	{
		return $this->get( 'supplier.label', '' );
	}


	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setLabel( string $value ) : \Aimeos\MShop\Supplier\Item\Iface
	{
		return $this->set( 'supplier.label', $value );
	}


	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode() : string
	{
		return $this->get( 'supplier.code', '' );
	}


	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Supplier\Item\Iface
	{
		return $this->set( 'supplier.code', $this->checkCode( $value ) );
	}


	/**
	 * Returns the position of the supplier item.
	 *
	 * @return int Position of the item
	 */
	public function getPosition() : int
	{
		return $this->get( 'supplier.position', 0 );
	}


	/**
	 * Sets the new position of the supplier item.
	 *
	 * @param int $position Position of the item
	 * @return \Aimeos\MShop\Rule\Item\Iface Rule item for chaining method calls
	 */
	public function setPosition( int $position ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'supplier.position', $position );
	}



	/**
	 * Returns the status of the item
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( 'supplier.status', 1 );
	}


	/**
	 * Sets the new status of the supplier item.
	 *
	 * @param int $value status of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setStatus( int $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'supplier.status', $value );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'supplier.code': $item->setCode( $value ); break;
				case 'supplier.label': $item->setLabel( $value ); break;
				case 'supplier.status': $item->setStatus( (int) $value ); break;
				case 'supplier.position': $item->setPosition( (int) $value ); break;
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

		$list['supplier.code'] = $this->getCode();
		$list['supplier.label'] = $this->getLabel();
		$list['supplier.status'] = $this->getStatus();
		$list['supplier.position'] = $this->getPosition();

		return $list;
	}

}
