<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $values;

	/**
	 * Initializes the order base coupon item.
	 *
	 * @param array $values Associative list of order coupon values
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.base.coupon.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the base ID of the order.
	 *
	 * @return integer|null Order base ID
	 */
	public function getBaseId()
	{
		if( isset( $this->values['order.base.coupon.baseid'] ) ) {
			return (int) $this->values['order.base.coupon.baseid'];
		}

		return null;
	}


	/**
	 * Sets the Base ID of the order.
	 *
	 * @param integer $baseid Order base ID.
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setBaseId( $baseid )
	{
		if( $baseid == $this->getBaseId() ) { return $this; }

		$this->values['order.base.coupon.baseid'] = (int) $baseid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the ID of the ordered product.
	 *
	 * @return integer|null ID of the ordered product.
	 */
	public function getProductId()
	{
		if( isset( $this->values['order.base.coupon.ordprodid'] ) ) {
			return (int) $this->values['order.base.coupon.ordprodid'];
		}

		return null;
	}


	/**
	 * Sets the ID of the ordered product.
	 *
	 * @param integer $productid ID of the ordered product
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setProductId( $productid )
	{
		if( $productid == $this->getProductId() ) { return $this; }

		$this->values['order.base.coupon.ordprodid'] = (int) $productid;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the coupon code.
	 *
	 * @return string|null Coupon code.
	 */
	public function getCode()
	{
		if( isset( $this->values['order.base.coupon.code'] ) ) {
			return (string) $this->values['order.base.coupon.code'];
		}

		return null;
	}


	/**
	 * Sets the coupon code.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface Order base coupon item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code == $this->getCode() ) { return $this; }

		$this->values['order.base.coupon.code'] = (string) $this->checkCode( $code );;
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
		return 'order/base/coupon';
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
				case 'order.base.coupon.baseid': $this->setBaseId( $value ); break;
				case 'order.base.coupon.productid': $this->setProductId( $value ); break;
				case 'order.base.coupon.code': $this->setCode( $value ); break;
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

		$list['order.base.coupon.productid'] = $this->getProductId();
		$list['order.base.coupon.code'] = $this->getCode();

		if( $private === true ) {
			$list['order.base.coupon.baseid'] = $this->getBaseId();
		}

		return $list;
	}

}
