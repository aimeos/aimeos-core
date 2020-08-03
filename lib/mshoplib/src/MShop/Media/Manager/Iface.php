<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;


/**
 * Generic interface for media managers.
 *
 * @package MShop
 * @subpackage Media
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\ListRef\Iface,
		\Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item New item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Media\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Media\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Media\Item\Iface;
}
