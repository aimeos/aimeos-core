<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Position;


/**
 * Common interface for items that carry sorting informations.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the position of the item in the list.
	 *
	 * @return integer Position of the item in the list
	 */
	public function getPosition() : int;

	/**
	 * Sets the new position of the item in the list.
	 *
	 * @param int $pos position of the item in the list
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Common\Item\Iface;
}
