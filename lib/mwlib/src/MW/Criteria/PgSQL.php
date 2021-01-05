<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria;


/**
 * PostgreSQL search class
 *
 * @package MW
 * @subpackage Common
 */
class PgSQL extends \Aimeos\MW\Criteria\SQL
{
	/**
	 * Creates a new compare expression.
	 *
	 * Available comparision operators are:
	 * "==": item EQUAL value
	 * "!=": item NOT EQUAL value
	 * "~=": item LIKE value
	 * "=~": item STARTS WITH value
	 * ">=": item GREATER OR EQUAL value
	 * "<=": item SMALLER OR EQUAL value
	 * ">": item GREATER value
	 * "<": item SMALLER value
	 *
	 * @param string $operator One of the known operators
	 * @param string $name Name of the variable or column that should be used for comparison
	 * @param mixed $value Value of the variable or column should be compared to
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
	 */
	public function compare( string $operator, string $name, $value ) : \Aimeos\MW\Criteria\Expression\Compare\Iface
	{
		return new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->getConnection(), $operator, $name, $value );
	}
}
