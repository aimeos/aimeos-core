<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Generic interface for coupons models implementing the coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
{
	/**
	 * Adds the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return void
	 */
	public function addCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base );

	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return void
	 */
	public function updateCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base );

	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return void
	 */
	public function deleteCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base );

	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes );

	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE();

	/**
	 * Tests if a coupon should be granted.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base );

	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object );
}
