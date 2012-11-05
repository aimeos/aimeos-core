<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 * @version $Id: Abstract.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Base class with required constants for result objects
 *
 * @package MW
 * @subpackage DB
 */
abstract class MW_DB_Result_Abstract
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
