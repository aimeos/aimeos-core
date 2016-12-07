<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Result;


/**
 * Base class with required constants for result objects
 *
 * @package MW
 * @subpackage DB
 */
abstract class Base
{
	/**
	 * Fetch mode returning numerically indexed record arrays
	 */
	const FETCH_NUM = 0;

	/**
	 * Fetch mode returning associative indexed record arrays
	 */
	const FETCH_ASSOC = 1;
}
