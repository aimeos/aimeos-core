<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Iterator;


/**
 * Common interface for manager iterators
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Iterator
{
	/**
	 * Terminates the iterator
	 */
	public function close() : void;
}
