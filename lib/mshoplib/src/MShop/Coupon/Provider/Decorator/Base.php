<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Base decorator methods for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class MShop_Coupon_Provider_Decorator_Base
	extends MShop_Coupon_Provider_Base
{
	private $provider;
	private $object;


	/**
	 * Initializes a new coupon provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Coupon_Item_Iface $couponItem Coupon item with configuration for the provider
	 * @param string $code Coupon code entered by the customer
	 * @param MShop_Coupon_Provider_Iface $provider Coupon provider interface
	 */
	public function __construct( MShop_Context_Item_Iface $context,
		MShop_Coupon_Item_Iface $couponItem, $code, MShop_Coupon_Provider_Iface $provider )
	{
		$this->provider = $provider;

		parent::__construct( $context, $couponItem, $code );
	}


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Iface $base )
	{
		$this->provider->addCoupon( $base );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Iface $base )
	{
		$this->provider->updateCoupon( $base );
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Iface $base )
	{
		$this->provider->deleteCoupon( $base );
	}


	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param MShop_Order_Item_Base_Iface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Iface $base )
	{
		return $this->provider->isAvailable( $base );
	}


	/**
	 * Sets the reference of the outside object.
	 *
	 * @param MShop_Coupon_Provider_Iface $object Reference to the outside provider or decorator
	 */
	public function setObject( MShop_Coupon_Provider_Iface $object )
	{
		$this->provider->setObject( $object );
		$this->object = $object;
	}


	/**
	 * Returns the outmost decorator or a reference to the provider itself.
	 *
	 * @return MShop_Coupon_Provider_Iface Outmost object
	 */
	protected function getObject()
	{
		if( isset( $this->object ) ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the stored provider object.
	 *
	 * @return MShop_Coupon_Provider_Iface Coupon provider
	 */
	protected function getProvider()
	{
		return $this->provider;
	}
}