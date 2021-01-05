<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Statement;


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
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn )
	{
		$this->conn = $conn;
	}


	/**
	 * Returns the connection object
	 *
	 * @return \Aimeos\MW\DB\Connection\Iface Connection object
	 */
	protected function getConnection() : \Aimeos\MW\DB\Connection\Iface
	{
		return $this->conn;
	}


	/**
	 * Returns the PDO type mapped to the Aimeos type
	 *
	 * @param integer $type Type of given value defined in \Aimeos\MW\DB\Statement\Base as constant
	 * @param mixed $value Value which should be bound to the placeholder
	 * @return integer PDO parameter type constant
	 * @throws \Aimeos\MW\DB\Exception If the type is unknown
	 */
	protected function getPdoType( int $type, $value ) : int
	{
		switch( $type )
		{
			case \Aimeos\MW\DB\Statement\Base::PARAM_NULL:
				$pdotype = \PDO::PARAM_NULL; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$pdotype = \PDO::PARAM_BOOL; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$pdotype = \PDO::PARAM_INT; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$pdotype = \PDO::PARAM_STR; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
				$pdotype = \PDO::PARAM_STR; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_LOB:
				$pdotype = \PDO::PARAM_LOB; break;
			default:
				throw new \Aimeos\MW\DB\Exception( sprintf( 'Invalid parameter type "%1$s"', $type ) );
		}

		if( is_null( $value ) ) {
			$pdotype = \PDO::PARAM_NULL;
		}

		return $pdotype;
	}
}
