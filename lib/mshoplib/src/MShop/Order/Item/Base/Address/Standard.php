<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Address;


/**
 * Default order address container object
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base\Address\Base
	implements \Aimeos\MShop\Order\Item\Base\Address\Iface
{
	private $values;

	/**
	 * Initializes the objects with the given array of values.
	 *
	 * @param array $values List of address elements
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.base.address.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return integer|null Base ID
	 */
	public function getBaseId()
	{
		if( isset( $this->values['order.base.address.baseid'] ) ) {
			return (int) $this->values['order.base.address.baseid'];
		}

		return null;
	}


	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param integer|null $value New base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setBaseId( $value )
	{
		if( $value == $this->getBaseId() ) { return $this; }

		$this->values['order.base.address.baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId()
	{
		if( isset( $this->values['order.base.address.addressid'] ) ) {
			return (string) $this->values['order.base.address.addressid'];
		}

		return '';
	}


	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setAddressId( $addrid )
	{
		if( $addrid == $this->getAddressId() ) { return $this; }

		$this->values['order.base.address.addressid'] = (string) $addrid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the address type which can be billing or delivery.
	 *
	 * @return string Address type
	 */
	public function getType()
	{
		if( isset( $this->values['order.base.address.type'] ) ) {
			return (string) $this->values['order.base.address.type'];
		}

		return \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY;
	}


	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return $this; }

		$this->checkType( $type );

		$this->values['order.base.address.type'] = (string) $type;
		$this->setModified();

		return $this;
	}


	/**
	 * Copys all data from a given address item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item New address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $item )
	{
		parent::copyFrom( $item );

		$this->setAddressId( $item->getId() );
		$this->setModified();

		return $this;
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
				case 'order.base.address.baseid': $this->setBaseId( $value ); break;
				case 'order.base.address.addressid': $this->setAddressId( $value ); break;
				case 'order.base.address.type': $this->setType( $value ); break;
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

		$list['order.base.address.type'] = $this->getType();

		if( $private === true )
		{
			$list['order.base.address.baseid'] = $this->getBaseId();
			$list['order.base.address.addressid'] = $this->getAddressId();
		}

		return $list;
	}

}
