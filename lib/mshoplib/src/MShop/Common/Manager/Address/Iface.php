<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Address;


/**
 * Interface for common address DAOs used by the shop.
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Saves a common address item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item common address item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Address\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Address\Iface;
}
