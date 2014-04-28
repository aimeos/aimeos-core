<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Abstract.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Base decorator methods for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class MShop_Coupon_Provider_Decorator_Abstract
	implements MShop_Coupon_Provider_Decorator_Interface
{
	private $_object;
	private $_context;
	private $_item;
	private $_code;
	private $_outer;


	/**
	 * Initializes a new coupon provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Coupon_Item_Interface $couponItem Coupon item with configuration for the provider
	 * @param string $code Coupon code entered by the customer
	 * @param MShop_Coupon_Provider_Interface $provider Coupon provider or decorator
	 */
	public function __construct(MShop_Context_Item_Interface $context,
		MShop_Coupon_Item_Interface $couponItem, $code, MShop_Coupon_Provider_Interface $provider, &$outer )
	{
		$this->_object = $provider;
		$this->_context = $context;
		$this->_item = $couponItem;
		$this->_code = $code;
		$this->_outer = &$outer;
	}


	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function addCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_object->addCoupon( $base );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_object->updateCoupon( $base );
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$this->_object->deleteCoupon( $base );
	}


	/**
	 *
	 * @param MShop_Order_Item_Base_Interface $base
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		return $this->_object->isAvailable( $base );
	}


	/**
	 * Returns the stored context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the coupon code the provider is responsible for.
	 *
	 * @return string Coupon code
	 */
	protected function _getCode()
	{
		return $this->_code;
	}


	/**
	 * Returns the stored coupon item.
	 *
	 * @return MShop_Coupon_Item_Interface Coupon item
	 */
	protected function _getItem()
	{
		return $this->_item;
	}


	/**
	 * Returns the stored provider object.
	 *
	 * @return MShop_Coupon_Provider_Interface Coupon provider
	 */
	protected function _getProvider()
	{
		return $this->_object;
	}


	/**
	 * Returns the last decorator
	 *
	 * @return MShop_Coupon_Provider_Decorator_Interface outer object
	 */
	protected function _getOuterObject()
	{
		return $this->_outer;
	}
}