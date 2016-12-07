<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Connection;


/**
 * Common class for all database connection implementations.
 *
 * @package MW
 * @subpackage DB
 */
abstract class Base
{
	/**
	 * Simple (direct) SQL queries
	 */
	const TYPE_SIMPLE = 0;

	/**
	 * Prepared statements
	 */
	const TYPE_PREP = 1;


	/**
	 * Escapes the value if necessary for direct inclusion in SQL statement.
	 *
	 * @param string $data Value to escape
	 * @return string Escaped string
	 */
	public function escape($data)
	{
		$quoted = $this->getRawObject()->quote( $data );

		if( $quoted[0] === '\'' ) {
			$quoted = substr( $quoted, 1, strlen( $quoted ) - 2 );
		}

		return $quoted;
	}
}
