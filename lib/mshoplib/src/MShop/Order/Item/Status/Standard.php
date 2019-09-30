<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	public function getParentId()
	{
		return $this->get( 'order.status.parentid' );
	}


	/**
	 * Sets the parentid of the order status.
	 *
	 * @param string $parentid Parent ID of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setParentId( $parentid )
	{
		return $this->set( 'order.status.parentid', (string) $parentid );
	}


	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType()
	{
		return (string) $this->get( 'order.status.type', '' );
	}


	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'order.status.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue()
	{
		return (string) $this->get( 'order.status.value', '' );
	}


	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function setValue( $value )
	{
		return $this->set( 'order.status.value', (string) $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Status\Iface Order status item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
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
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
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
