<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
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
	/**
	 * Initializes the object
	 *
	 * @param array $values Associative list of key/value pairs with order status properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.basket.', $values );
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
	 * Returns the content of the basket.
	 *
	 * @return string Content of the basket
	 */
	public function getContent() : string
	{
		return (string) $this->get( 'order.basket.content' );
	}


	/**
	 * Sets the content of the basket.
	 *
	 * @param string $value Content of the basket
	 * @return \Aimeos\MShop\Order\Item\Basket\Iface Basket item for chaining method calls
	 */
	public function setContent( string $value ) : \Aimeos\MShop\Order\Item\Basket\Iface
	{
		return $this->set( 'order.basket.content', $value );
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
				case 'order.basket.content': $item = $item->setContent( $value ); break;
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
		$list['order.basket.content'] = $this->getContent();
		$list['order.basket.customerid'] = $this->getCustomerId();

		return $list;
	}

}
