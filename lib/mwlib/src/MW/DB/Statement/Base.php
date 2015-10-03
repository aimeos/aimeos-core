<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Base class for all statement implementations providing the parameter constants
 *
 * @package MW
 * @subpackage DB
 */
abstract class MW_DB_Statement_Base
{
	/**
	 * NULL values
	 */
	const PARAM_NULL = 0;

	/**
	 * Boolean (true/false) values
	 */
	const PARAM_BOOL = 1;

	/**
	 * 32bit integer values
	 */
	const PARAM_INT = 2;

	/**
	 * 32bit floating point values
	 */
	const PARAM_FLOAT = 3;

	/**
	 * String values
	 */
	const PARAM_STR = 4;

	/**
	 * Large objects
	 */
	const PARAM_LOB = 5;
}
