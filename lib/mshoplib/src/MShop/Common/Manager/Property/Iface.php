<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Property;


/**
 * Default property manager implementation
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Inserts the new property items for product item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Property item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Property\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Property\Iface;
}
