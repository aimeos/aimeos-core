<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MAdmin
 * @subpackage Log
 */


namespace Aimeos\MAdmin\Log\Manager;


/**
 * Interface for log manager implementations.
 *
 * @package MAdmin
 * @subpackage Log
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MW\Logger\Iface
{
	/**
	 * Adds a new log to the storage.
	 *
	 * @param \Aimeos\MAdmin\Log\Item\Iface $item Log item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MAdmin\Log\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Log\Item\Iface;
}
