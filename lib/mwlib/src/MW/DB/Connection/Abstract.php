<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Common class for all database connection implementations.
 *
 * @package MW
 * @subpackage DB
 */
abstract class MW_DB_Connection_Abstract
{
	/**
	 * Simple (direct) SQL queries
	 */
	const TYPE_SIMPLE = 0;

	/**
	 * Prepared statements
	 */
	const TYPE_PREP = 1;
}
