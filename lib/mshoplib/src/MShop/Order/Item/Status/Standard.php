<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Status;


/**
 * Default implementation of the order status object.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Status\Base
	implements \Aimeos\MShop\Order\Item\Status\Iface
{
	/**
	 * Initializes the object
	 *
	 * @param array $values Associative list of key/value pairs with order status properties
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.status.', $values );
	}


	/**
	 * Returns the parentid of the order status.
	 *
	 * @return string|null Parent ID of the order
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.status.parentid' );
	}


	/**
	 * Sets the parentid of the order status.
	 *
	 * @param string|null $parentid Parent ID of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.status.parentid', $parentid );
	}


	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType() : string
	{
		return $this->get( 'order.status.type', '' );
	}


	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.status.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue() : string
	{
		return (string) $this->get( 'order.status.value', '' );
	}


	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setValue( string $value ) : \Aimeos\MShop\Order\Item\Status\Iface
	{
		return $this->set( 'order.status.value', $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.status.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.status.type': $item = $item->setType( $value ); break;
				case 'order.status.value': $item = $item->setValue( $value ); break;
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

		$list['order.status.type'] = $this->getType();
		$list['order.status.value'] = $this->getValue();

		if( $private === true ) {
			$list['order.status.parentid'] = $this->getParentId();
		}

		return $list;
	}

}
