<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Dummy database connection class.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Connection_None implements MW_DB_Connection_Interface
{
	/**
	 * Throws MW_DB_Exception.
	 *
	 * @param string $sql SQL statement, maybe with place holders
	 * @param integer $type Simple or prepared statement type constant from abstract class
	 * @throws MW_DB_Exception
	 */
	public function create($sql, $type = MW_DB_Connection_Abstract::TYPE_SIMPLE)
	{
		throw new MW_DB_Exception( 'This method is not implemented in the MW_DB_Connection_None' );
	}


	/**
	 * Throws MW_DB_Exception.
	 *
	 * @param string $data Value to escape
	 *
	 * @throws MW_DB_Exception
	 */
	public function escape($data)
	{
		throw new MW_DB_Exception( 'This method is not implemented in the MW_DB_Connection_None' );
	}


	/**
	 * Throws MW_DB_Exception.
	 *
	 * @throws MW_DB_Exception
	 */
	public function begin()
	{
		throw new MW_DB_Exception( 'This method is not implemented in the MW_DB_Connection_None' );
	}


	/**
	 * Throws MW_DB_Exception.
	 *
	 * @throws MW_DB_Exception
	 */
	public function commit()
	{
		throw new MW_DB_Exception( 'This method is not implemented in the MW_DB_Connection_None' );
	}


	/**
	 * Throws MW_DB_Exception.
	 *
	 * @throws MW_DB_Exception
	 */
	public function rollback()
	{
		throw new MW_DB_Exception( 'This method is not implemented in the MW_DB_Connection_None' );
	}
}