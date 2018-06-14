<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Common interface for items containing property items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the property items of the product
	 *
	 * @param string|null $type Name of the property item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Product\Item\Property\Iface[] Associative list of property IDs as keys and property items as values
	 */
	public function getPropertyItems( $type = null, $active = true );
}
