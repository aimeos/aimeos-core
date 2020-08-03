<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MAdmin
 * @subpackage Cache
 */


namespace Aimeos\MAdmin\Cache\Manager;


/**
 * Interface for cache manager implementations.
 *
 * @package MAdmin
 * @subpackage Cache
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Returns the cache object
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache() : \Aimeos\MW\Cache\Iface;

	/**
	 * Adds a new cache to the storage.
	 *
	 * @param \Aimeos\MAdmin\Cache\Item\Iface $item Cache item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MAdmin\Cache\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Cache\Item\Iface;
}
