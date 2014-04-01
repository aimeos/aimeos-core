<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * Common class for all cache classes.
 *
 * @package MW
 * @subpackage Cache
 */
abstract class MW_Cache_Abstract
	implements MW_Cache_Interface
{
	/**
	 * Removes all expired cache entries.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
	}


	/**
	 * Tests if caching is available.
	 *
	 * @inheritDoc
	 *
	 * @return boolean True if available, false if not
	 * @deprecated
	 */
	public function isAvailable()
	{
		return true;
	}
}
