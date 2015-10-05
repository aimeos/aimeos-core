<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Item\Code;


/**
 * Generic interface for coupon codes created and saved by the coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Time\Iface
{
	/**
	 * Returns the unique ID of the coupon item the code belongs to.
	 *
	 * @return integer Unique ID of the coupon item
	 */
	public function getCouponId();


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param integer $id Unique ID of the coupon item
	 * @return void
	 */
	public function setCouponId( $id );


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string Coupon code
	 */
	public function getCode();


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 * @return void
	 */
	public function setCode( $code );


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return integer Number of available tries
	 */
	public function getCount();


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param integer $count Number of tries
	 * @return void
	 */
	public function setCount( $count );
}
