<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Time\Iface
{
	/**
	 * Returns the label of the coupon if available.
	 *
	 * @return string Name/label of the coupon item
	 */
	public function getLabel();

	/**
	 * Sets the label of the coupon item.
	 *
	 * @param string $name Name/label of the coupon item.
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setLabel( $name );

	/**
	 * Returns the provider of the coupon.
	 *
	 * @return string Name of the provider which is the short provider class name
	 */
	public function getProvider();

	/**
	 * Sets the new provider of the coupon item which is the short name of the provider class name.
	 *
	 * @param string $provider Coupon provider, esp. short provider class name
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setProvider( $provider );

	/**
	 * Returns the configuration of the coupon item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig();

	/**
	 * Sets the new configuration for the coupon item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setConfig( array $config );

	/**
	 * Returns the status of the coupon item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the new status of the coupon item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item for chaining method calls
	 */
	public function setStatus( $status );

}
