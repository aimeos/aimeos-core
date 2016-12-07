<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Time\Iface,
	\Aimeos\MShop\Common\Item\Parentid\Iface
{
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
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
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
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCount( $count );
}
