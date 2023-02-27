<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Basket;


/**
 * Default implementation of the order status object.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Basket\Iface
{
	private ?\Aimeos\MShop\Order\Item\Iface $item;


	/**
	 * Initializes the object
	 *
	 * @param array $values Associative list of key/value pairs with order status properties
	 * @param \Aimeos\MShop\Order\Item\Iface|null $item Basket object
	 */
	public function __construct( array $values = [], \Aimeos\MShop\Order\Item\Iface $item = null )
	{
		parent::__construct( 'order.basket.', $values );
		$this->item = $item;
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return parent::setId( $id )->setModified();
	}


	/**
	 * Returns the basket object.
	 *
	 * @return \Aimeos\MShop\Order\Item\Iface|null $basket Basket object
	 */
	public function getItem() : ?\Aimeos\MShop\Order\Item\Iface
	{
		return $this->item;
	}


	/**
	 * Sets the basket object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setItem( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Item\Basket\Iface
	{
		$this->item = $basket;
		return $this->setModified();
	}


	/**
	 * Returns the ID of the customer who owns the basket.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string
	{
		return (string) $this->get( 'order.basket.customerid', '' );
	}


	/**
	 * Sets the ID of the customer who owned the basket.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setCustomerId( ?string $value ) : \Aimeos\MShop\Order\Item\Basket\Iface
	{
		return $this->set( 'order.basket.customerid', (string) $value );
	}


	/**
	 * Returns the name of the basket.
	 *
	 * @return string Name for the basket
	 */
	public function getName() : string
	{
		return (string) $this->get( 'order.basket.name', '' );
	}


	/**
	 * Sets the name of the basket.
	 *
	 * @param string $value Name for the basket
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setName( ?string $value ) : \Aimeos\MShop\Order\Item\Basket\Iface
	{
		return $this->set( 'order.basket.name', (string) $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/basket';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Order status item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.basket.customerid': $item = $item->setCustomerId( $value ); break;
				case 'order.basket.name': $item = $item->setName( $value ); break;
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

		$list['order.basket.name'] = $this->getName();
		$list['order.basket.customerid'] = $this->getCustomerId();

		return $list;
	}

}
