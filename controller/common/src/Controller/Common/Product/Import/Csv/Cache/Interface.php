<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Common cache interface for CSV import caches
 *
 * @package Controller
 * @subpackage Common
 */
interface Controller_Common_Product_Import_Csv_Cache_Interface
{
	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context );

	/**
	 * Returns the item or ID for the given code
	 *
	 * @param string $code Unique code of the item
	 * @return MShop_Common_Item_Interface|string|null Item object, unique ID or null if not found
	 */
	public function get( $code, $type = null );

	/**
	 * Adds the item or ID to the cache
	 *
	 * @param MShop_Common_Item_Interface $item Item object
	 */
	public function set( MShop_Common_Item_Interface $item );
}
