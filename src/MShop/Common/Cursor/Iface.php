<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Cursor;


/**
 * Common interface for manager cursors
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param \Aimeos\Base\DB\Result\Iface $result Result set to iterate over
	 */
	public function __construct( \Aimeos\Base\Criteria\Iface $filter );

	/**
	 * Returns the filter criteria object
	 *
	 * @return \Aimeos\Base\Criteria\Iface Filter criteria object
	 */
	public function filter() : \Aimeos\Base\Criteria\Iface;

	/**
	 * Sets the new cursor value
	 *
	 * @return mixed $value Cursor value
	 */
	public function setValue( $value ) : \Aimeos\MShop\Common\Cursor\Iface;

	/**
	 * Returns the cursor value
	 *
	 * @return mixed Cursor value
	 */
	public function value();
}
