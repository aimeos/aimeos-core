<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Generic interface for coupon codes created and saved by the coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
interface MShop_Coupon_Item_Code_Interface extends MShop_Common_Item_Interface
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
	 */
	public function setCount( $count );


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart();


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date );


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd();


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date );

}
