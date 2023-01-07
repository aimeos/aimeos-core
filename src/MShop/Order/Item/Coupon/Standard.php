<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Coupon;


/**
 * Default implementation for order item base coupon.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Coupon\Iface
{
	/**
	 * Initializes the order base coupon item.
	 *
	 * @param array $values Associative list of order coupon values
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.coupon.', $values );
	}


	/**
	 * Returns the base ID of the order.
	 *
	 * @return string|null Order base ID
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.coupon.parentid' );
	}


	/**
	 * Sets the Base ID of the order.
	 *
	 * @param string|null $parentid Order base ID.
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : \Aimeos\MShop\Order\Item\Coupon\Iface
	{
		return $this->set( 'order.coupon.parentid', (string) $parentid );
	}


	/**
	 * Returns the ID of the ordered product.
	 *
	 * @return string|null ID of the ordered product.
	 */
	public function getProductId() : ?string
	{
		return $this->get( 'order.coupon.ordprodid' );
	}


	/**
	 * Sets the ID of the ordered product.
	 *
	 * @param string $productid ID of the ordered product
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setProductId( string $productid ) : \Aimeos\MShop\Order\Item\Coupon\Iface
	{
		return $this->set( 'order.coupon.ordprodid', (string) $productid );
	}


	/**
	 * Returns the coupon code.
	 *
	 * @return string|null Coupon code.
	 */
	public function getCode() : ?string
	{
		return $this->get( 'order.coupon.code' );
	}


	/**
	 * Sets the coupon code.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Coupon\Iface
	{
		return $this->set( 'order.coupon.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/coupon';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface Order coupon item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.coupon.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.coupon.productid': !$private ?: $item = $item->setProductId( $value ); break;
				case 'order.coupon.code': $item = $item->setCode( $value ); break;
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

		$list['order.coupon.code'] = $this->getCode();

		if( $private === true )
		{
			$list['order.coupon.parentid'] = $this->getParentId();
			$list['order.coupon.productid'] = $this->getProductId();
		}

		return $list;
	}

}
