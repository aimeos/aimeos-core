<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Supplier\Item\Iface
{
	private $addresses;
	private $sortedAddr;
	private $values;

	/**
	 * Initializes the supplier item object
	 *
	 * @param array $values List of attributes that belong to the supplier item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [], $addresses = [] )
	{
		parent::__construct( 'supplier.', $values, $listItems, $refItems );

		$this->addresses = $addresses;
		$this->values = $values;
	}


	/**
	 * Returns the address items of the supplier
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of IDs as keys and items as values
	 */
	public function getAddressItems()
	{
		if( $this->sortedAddr === null )
		{
			$fcn = function( $a, $b )
			{
				if( $a->getPosition() == $b->getPosition() ) {
					return 0;
				}

				return ( $a->getPosition() < $b->getPosition() ? -1 : 1 );
			};

			uasort( $this->addresses, $fcn );
			$this->sortedAddr = true;
		}

		return $this->addresses;
	}


	/**
	 * Returns the label of the supplier item.
	 *
	 * @return string label of the supplier item
	 */
	public function getLabel()
	{
		if( isset( $this->values['supplier.label'] ) ) {
			return (string) $this->values['supplier.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setLabel( $value )
	{
		if( $value == $this->getLabel() ) { return $this; }

		$this->values['supplier.label'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode()
	{
		if( isset( $this->values['supplier.code'] ) ) {
			return (string) $this->values['supplier.code'];
		}

		return '';
	}


	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setCode( $value )
	{
		if( $value == $this->getCode() ) { return $this; }

		$this->values['supplier.code'] = (string) $this->checkCode( $value );;
		$this->setModified();

		return $this;
	}



	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['supplier.status'] ) ) {
			return (int) $this->values['supplier.status'];
		}

		return 0;
	}


	/**
	 * Sets the new status of the supplier item.
	 *
	 * @param integer $value status of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Supplier item for chaining method calls
	 */
	public function setStatus( $value )
	{
		if( $value == $this->getStatus() ) { return $this; }

		$this->values['supplier.status'] = (int) $value;
		$this->setModified();

		return $this;
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
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'supplier.code': $this->setCode( $value ); break;
				case 'supplier.label': $this->setLabel( $value ); break;
				case 'supplier.status': $this->setStatus( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
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
