<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Cache;


/**
 * Common cache interface for CSV import caches
 *
 * @package Controller
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context );

	/**
	 * Returns the item or ID for the given code
	 *
	 * @param string $code Unique code of the item
	 * @param string|null $type Item type if used and required
	 * @return \Aimeos\MShop\Common\Item\Iface|string|null Item object, unique ID or null if not found
	 */
	public function get( $code, $type = null );

	/**
	 * Adds the item or ID to the cache
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 */
	public function set( \Aimeos\MShop\Common\Item\Iface $item );
}
