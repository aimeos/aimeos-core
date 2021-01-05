<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @return string|null Coupon code
	 */
	public function getCode() : ?string;


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Coupon\Item\Code\Iface;


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return int|null Number of available tries or null for unlimited
	 */
	public function getCount() : ?int;


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param int|null $count Number of tries or null for unlimited
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCount( $count = null ) : \Aimeos\MShop\Coupon\Item\Code\Iface;


	/**
	 * Returns reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @return string Arbitrary value depending on the coupon provider
	 */
	public function getRef() : string;


	/**
	 * Sets the new reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @param string|null $ref Arbitrary value depending on the coupon provider
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setRef( ?string $ref ) : \Aimeos\MShop\Coupon\Item\Code\Iface;
}
