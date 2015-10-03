<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Basic methods and constants for order base items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
abstract class MShop_Order_Manager_Base_Base
	extends MShop_Common_Manager_Base
	implements MShop_Order_Manager_Base_Iface
{
	/**
	 * Unlock basket.
	 * Disable the lock for the serialized basket in the session so
	 * modifications of the basket content are allowed again. Note that the
	 * locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_DISABLE = 0;

	/**
	 * Lock basket.
	 * Enable the lock for the serialized basket in the session so
	 * modifications of the basket content are not allowed any more. Note that
	 * the locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_ENABLE = 1;


	/**
	 * Load/store no additional order information
	 */
	const PARTS_NONE = 0;

	/**
	 * Load/store order addresses
	 * Only the addresses of the order will be loaded additionally to the base
	 * order information.
	 */
	const PARTS_ADDRESS = 1;

	/**
	 * Load/store order coupons
	 * Only the coupon information stored in the order will be loaded additionally
	 * to the base order information.
	 */
	const PARTS_COUPON = 2;

	/**
	 * Load/store order products
	 * Only the ordered products and their associated data of the order will be
	 * loaded additionally to the base order information.
	 */
	const PARTS_PRODUCT = 4;

	/**
	 * Load/store order services
	 * Only the services (delivery, payment, etc.) and their associated data of
	 * the order will be loaded additionally to the base order information.
	 */
	const PARTS_SERVICE = 8;

	/**
	 * Load/store all order content
	 * The complete order with all associated data will be loaded additionally
	 * to the base order information. This is the same as the basket content
	 * of the customer when purchased.
	 */
	const PARTS_ALL = 15;


	/**
	 * Checks if the lock value is a valid constant.
	 *
	 * @param integer $value Lock constant
	 * @throws MShop_Order_Exception If given value is invalid
	 */
	protected function checkLock( $value )
	{
		switch( $value )
		{
			case MShop_Order_Manager_Base_Base::LOCK_DISABLE:
			case MShop_Order_Manager_Base_Base::LOCK_ENABLE:
				break;
			default:
				throw new MShop_Order_Exception( sprintf( 'Lock flag "%1$d" not within allowed range', $value ) );
		}
	}
}
