<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Item;


/**
 * Generic interface for coupons created and saved by the coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\Time\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the label of the coupon if available.
	 *
	 * @return string Name/label of the coupon item
	 */
	public function getLabel() : string;

	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Name/label of the coupon item.
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setLabel( string $name ) : \Aimeos\MShop\Coupon\Item\Iface;

	/**
	 * Returns the provider of the coupon.
	 *
	 * @return string Name of the provider which is the short provider class name
	 */
	public function getProvider() : string;

	/**
	 * Sets the new provider of the coupon item which is the short name of the provider class name.
	 *
	 * @param string $provider Coupon provider, esp. short provider class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Coupon\Item\Iface;
}
