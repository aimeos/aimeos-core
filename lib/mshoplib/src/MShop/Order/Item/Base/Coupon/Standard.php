<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Coupon;


/**
 * Default implementation for order item base coupon.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Base\Coupon\Iface
{
	/**
	 * Initializes the order base coupon item.
	 *
	 * @param array $values Associative list of order coupon values
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.base.coupon.', $values );
	}


	/**
	 * Returns the base ID of the order.
	 *
	 * @return string|null Order base ID
	 */
	public function getBaseId() : ?string
	{
		return $this->get( 'order.base.coupon.baseid' );
	}


	/**
	 * Sets the Base ID of the order.
	 *
	 * @param string|null $baseid Order base ID.
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setBaseId( ?string $baseid ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface
	{
		return $this->set( 'order.base.coupon.baseid', (string) $baseid );
	}


	/**
	 * Returns the ID of the ordered product.
	 *
	 * @return string|null ID of the ordered product.
	 */
	public function getProductId() : ?string
	{
		return $this->get( 'order.base.coupon.ordprodid' );
	}


	/**
	 * Sets the ID of the ordered product.
	 *
	 * @param string $productid ID of the ordered product
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setProductId( string $productid ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface
	{
		return $this->set( 'order.base.coupon.ordprodid', (string) $productid );
	}


	/**
	 * Returns the coupon code.
	 *
	 * @return string|null Coupon code.
	 */
	public function getCode() : ?string
	{
		return $this->get( 'order.base.coupon.code' );
	}


	/**
	 * Sets the coupon code.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Coupon\Iface
	{
		return $this->set( 'order.base.coupon.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/base/coupon';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order coupon item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.coupon.baseid': !$private ?: $item = $item->setBaseId( $value ); break;
				case 'order.base.coupon.productid': !$private ?: $item = $item->setProductId( $value ); break;
				case 'order.base.coupon.code': $item = $item->setCode( $value ); break;
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

		$list['order.base.coupon.code'] = $this->getCode();

		if( $private === true )
		{
			$list['order.base.coupon.baseid'] = $this->getBaseId();
			$list['order.base.coupon.productid'] = $this->getProductId();
		}

		return $list;
	}

}
