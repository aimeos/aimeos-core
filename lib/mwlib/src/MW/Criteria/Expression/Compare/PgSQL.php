<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


/**
 * PostgreSQL implementation for comparing objects
 *
 * @package MW
 * @subpackage Common
 */
class PgSQL extends SQL
{
	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return double|string|integer Escaped value
	 */
	protected function escape( string $operator, string $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case \Aimeos\MW\DB\Statement\Base::PARAM_NULL:
				$value = 'null'; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$value = ( $value ? "'t'" : "'f'" ); break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$value = (int) (string) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$value = (double) (string) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
				if( $operator === '~=' ) {
					$value = '\'%' . str_replace( ['#', '%', '_', '['], ['##', '#%', '#_', '#['], $this->getConnection()->escape( (string) $value ) ) . '%\''; break;
				}
				if( $operator === '=~' ) {
					$value = '\'' . str_replace( ['#', '%', '_', '['], ['##', '#%', '#_', '#['], $this->getConnection()->escape( (string) $value ) ) . '%\''; break;
				}
				// all other operators: escape in default case
			default:
				$value = '\'' . $this->getConnection()->escape( (string) $value ) . '\'';
		}

		return $value;
	}
}
