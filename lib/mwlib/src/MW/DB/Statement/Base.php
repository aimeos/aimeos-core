<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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


	/**
	 * Creates the SQL string with bound parameters.
	 *
	 * @param array $parts List of SQL statement parts
	 * @param array $binds List of values for the markers
	 * @return string SQL statement
	 */
	protected function buildSQL( array $parts, array $binds )
	{
		$i = 1; $stmt = '';

		foreach( $parts as $part )
		{
			$stmt .= $part;
			if( isset( $binds[$i] ) ) {
				$stmt .= $binds[$i];
			}
			$i++;
		}

		return $stmt;
	}


	/**
	 * Returns the PDO type mapped to the Aimeos type
	 *
	 * @param integer $type Type of given value defined in \Aimeos\MW\DB\Statement\Base as constant
	 * @param mixed $value Value which should be bound to the placeholder
	 * @return integer PDO parameter type constant
	 * @throws \Aimeos\MW\DB\Exception If the type is unknown
	 */
	protected function getPdoType( $type, $value )
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


	/**
	 * Returns the SQL parts split at the markers
	 *
	 * @param string $sql SQL statement, mayby with markers
	 * @return array List of SQL parts split at the markers
	 * @throws \Aimeos\MW\DB\Exception If the SQL statement is invalid
	 */
	protected function getSqlParts( $sql )
	{
		$result = [];
		$parts = explode( '?', $sql );

		if( ( $part = reset( $parts ) ) !== false )
		{
			do
			{
				$count = 0; $temp = $part;
				while( ( $pr = str_replace( array( '\'\'', '\\\'' ), '', $part ) ) !== false
					&& ( $count += substr_count( $pr, '\'' ) ) % 2 !== 0 )
				{
					if( ( $part = next( $parts ) ) === false ) {
						throw new \Aimeos\MW\DB\Exception( 'Number of apostrophes don\'t match: ' . $sql );
					}
					$temp .= '?' . $part;
				}
				$result[] = $temp;
			}
			while( ( $part = next( $parts ) ) !== false );
		}

		return $result;
	}
}
