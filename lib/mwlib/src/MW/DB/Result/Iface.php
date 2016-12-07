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
 * Required functions for database result objects.
 *
 * @package MW
 * @subpackage DB
 */
interface Iface
{
	/**
	 * Returns the number of rows affected by a INSERT, UPDATE or DELETE statement.
	 *
	 * @return integer Number of touched records
	 */
	public function affectedRows();


	/**
	 * Retrieves the next row from database result set.
	 *
	 * @param integer $style The data can be returned as associative or numerical array
	 * @return List (numeric or associative array) of columns returned by the SQL statement
	 */
	public function fetch( $style = \Aimeos\MW\DB\Result\Base::FETCH_ASSOC );


	/**
	 * Cleans up pending database result sets.
	 * @return void
	 */
	public function finish();


	/**
	 * Retrieves next database result set.
	 *
	 * @return boolean True if another result is available, false if not
	 */
	public function nextResult();
}
