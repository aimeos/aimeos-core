<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Cart;


/**
 * Default implementation of the order status object.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Cart\Iface
{
	/**
	 * Initializes the object
	 *
	 * @param array $values Associative list of key/value pairs with order status properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.cart.', $values );
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
	 * Returns the content of the cart.
	 *
	 * @return string Content of the cart
	 */
	public function getContent() : string
	{
		return (string) $this->get( 'order.cart.content' );
	}


	/**
	 * Sets the content of the cart.
	 *
	 * @param string $value Content of the cart
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setContent( string $value ) : \Aimeos\MShop\Order\Item\Cart\Iface
	{
		return $this->set( 'order.cart.content', $value );
	}


	/**
	 * Returns the ID of the customer who owns the cart.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string
	{
		return (string) $this->get( 'order.cart.customerid', '' );
	}


	/**
	 * Sets the ID of the customer who owned the cart.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setCustomerId( ?string $value ) : \Aimeos\MShop\Order\Item\Cart\Iface
	{
		return $this->set( 'order.cart.customerid', (string) $value );
	}


	/**
	 * Returns the name of the cart.
	 *
	 * @return string Name for the cart
	 */
	public function getName() : string
	{
		return (string) $this->get( 'order.cart.name', '' );
	}


	/**
	 * Sets the name of the cart.
	 *
	 * @param string $value Name for the cart
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Cart item for chaining method calls
	 */
	public function setName( ?string $value ) : \Aimeos\MShop\Order\Item\Cart\Iface
	{
		return $this->set( 'order.cart.name', (string) $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/cart';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Order status item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.cart.customerid': $item = $item->setCustomerId( $value ); break;
				case 'order.cart.content': $item = $item->setContent( $value ); break;
				case 'order.cart.name': $item = $item->setName( $value ); break;
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

		$list['order.cart.name'] = $this->getName();
		$list['order.cart.content'] = $this->getContent();
		$list['order.cart.customerid'] = $this->getCustomerId();

		return $list;
	}

}
