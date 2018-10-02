<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus();

	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( $status );
}
