<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Item;


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
	use \Aimeos\MShop\Common\Item\ListRef\Traits {
		__clone as __cloneList;
	}
	use \Aimeos\MShop\Common\Item\AddressRef\Traits {
		__clone as __cloneAddress;
	}


	/**
	 * Initializes the supplier item object
	 *
	 * @param array $values List of attributes that belong to the supplier item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [], $addresses = [] )
	{
		parent::__construct( 'supplier.', $values );

		$this->initListItems( $listItems, $refItems );
		$this->initAddressItems( $addresses );
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
	public function getLabel()
	{
		return (string) $this->get( 'supplier.label', '' );
	}


	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setLabel( $value )
	{
		return $this->set( 'supplier.label', (string) $value );
	}


	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode()
	{
		return (string) $this->get( 'supplier.code', '' );
	}


	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setCode( $value )
	{
		return $this->set( 'supplier.code', $this->checkCode( $value ) );
	}



	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'supplier.status', 1 );
	}


	/**
	 * Sets the new status of the supplier item.
	 *
	 * @param integer $value status of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setStatus( $value )
	{
		return $this->set( 'supplier.status', (int) $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'supplier';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'supplier.code': $item = $item->setCode( $value ); break;
				case 'supplier.label': $item = $item->setLabel( $value ); break;
				case 'supplier.status': $item = $item->setStatus( $value ); break;
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

		$list['supplier.code'] = $this->getCode();
		$list['supplier.label'] = $this->getLabel();
		$list['supplier.status'] = $this->getStatus();

		return $list;
	}

}
