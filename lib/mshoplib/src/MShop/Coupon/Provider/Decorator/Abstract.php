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
abstract class MShop_Coupon_Provider_Decorator_Abstract
	extends MShop_Coupon_Provider_Abstract
{
	private $_provider;
	private $_object;


	/**
	 * Initializes a new coupon provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Coupon_Item_Interface $couponItem Coupon item with configuration for the provider
	 * @param string $code Coupon code entered by the customer
	 * @param MShop_Coupon_Provider_Interface $provider Coupon provider interface
	 */
	public function __construct(MShop_Context_Item_Interface $context,
		MShop_Coupon_Item_Interface $couponItem, $code, MShop_Coupon_Provider_Interface $provider )
	{
		$this->_provider = $provider;

		parent::__construct( $context, $couponItem, $code );
	}


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_provider->addCoupon( $base );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_provider->updateCoupon( $base );
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_provider->deleteCoupon( $base );
	}


	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		return $this->_provider->isAvailable( $base );
	}


	/**
	 * Sets the reference of the outside object.
	 *
	 * @param MShop_Coupon_Provider_Interface $object Reference to the outside provider or decorator
	 */
	public function setObject( MShop_Coupon_Provider_Interface $object )
	{
		$this->_provider->setObject( $object );
		$this->_object = $object;
	}


	/**
	 * Returns the outmost decorator or a reference to the provider itself.
	 *
	 * @return MShop_Coupon_Provider_Interface Outmost object
	 */
	protected function _getObject()
	{
		if( isset( $this->_object ) ) {
			return $this->_object;
		}

		return $this;
	}


	/**
	 * Returns the stored provider object.
	 *
	 * @return MShop_Coupon_Provider_Interface Coupon provider
	 */
	protected function _getProvider()
	{
		return $this->_provider;
	}
}