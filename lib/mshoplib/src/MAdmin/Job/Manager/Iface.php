<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Manager;


/**
 * Interface for job manager implementations.
 *
 * @package MAdmin
 * @subpackage Job
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Adds a new job to the storage.
	 *
	 * @param \Aimeos\MAdmin\Job\Item\Iface $item Job item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MAdmin\Job\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Job\Item\Iface;
}
