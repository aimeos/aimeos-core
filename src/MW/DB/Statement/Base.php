<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\Base\DB\Statement;


/**
 * Base class for all statement implementations providing the parameter constants
 *
 * @package MW
 * @subpackage DB
 */
abstract class Base
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


	private $conn;


	/**
	 * Initializes the base object
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection object
	 */
	public function __construct( \Aimeos\Base\DB\Connection\Iface $conn )
	{
		$this->conn = $conn;
	}


	/**
	 * Returns the connection object
	 *
	 * @return \Aimeos\Base\DB\Connection\Iface Connection object
	 */
	protected function getConnection() : \Aimeos\Base\DB\Connection\Iface
	{
		return $this->conn;
	}
}
