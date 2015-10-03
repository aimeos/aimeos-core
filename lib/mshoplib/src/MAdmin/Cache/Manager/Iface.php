<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * Interface for cache manager implementations.
 *
 * @package MAdmin
 * @subpackage Cache
 */
interface MAdmin_Cache_Manager_Iface
	extends MShop_Common_Manager_Factory_Iface
{
	/**
	 * Returns the cache object
	 *
	 * @return MW_Cache_Iface Cache object
	 */
	public function getCache();
}
