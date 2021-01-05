<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Status;


/**
 * Generic interface for items with status values
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the status of the common list type item
	 *
	 * @return int Status of the common list type item
	 */
	public function getStatus() : int;

	/**
	 * Sets the status of the common list type item
	 *
	 * @param int $status New status of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface;
}
