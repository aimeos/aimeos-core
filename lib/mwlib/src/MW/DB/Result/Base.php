<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
abstract class Base implements Iface
{
	/**
	 * Fetch mode returning numerically indexed record arrays
	 */
	const FETCH_NUM = 0;

	/**
	 * Fetch mode returning associative indexed record arrays
	 */
	const FETCH_ASSOC = 1;


	/**
	 * Retrieves all row from database result set.
	 *
	 * @param int $style The data can be returned as associative or numerical array
	 * @return array Numeric or associative array of columns returned by the database
	 */
	public function all( int $style = \Aimeos\MW\DB\Result\Base::FETCH_ASSOC ) : array
	{
		$list = [];

		while( $row = $this->fetch( $style ) ) {
			$list[] = $row;
		}

		return $list;
	}
}
