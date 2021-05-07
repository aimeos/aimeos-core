<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


/**
 * SQL implementation for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
class SQL extends Base
{
	private static $operators = ['=~' => 'LIKE', '~=' => 'LIKE', '==' => '=', '!=' => '<>', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', '-' => '-'];
	private $conn;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable or column that should be compared.
	 * @param mixed $value Value that the variable or column should be compared to
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, string $operator, string $name, $value )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		parent::__construct( $operator, $name, $value );
		$this->conn = $conn;
	}


	/**
	 * Returns the available operators for the expression.
	 *
	 * @return array List of available operators
	 */
	public static function getOperators() : array
	{
		return array_keys( self::$operators );
	}


	/**
	 * Creates a term string from the given parameters.
	 *
	 * @param string|array $name Translated name(s) of the variable or column
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return string Created term string (name operator value)
	 */
	protected function createTerm( $name, string $type, $value ) : string
	{
		$op = $this->getOperator();

		if( $op === '-' )
		{
			$p = explode( ' - ', $value );

			return $name . ' >= ' . $this->escape( '>=', $type, $p[0] )
				. ' AND ' . $name . ' <= ' . $this->escape( '<=', $type, $p[1] );
		}

		$term = $name . ' ' . self::$operators[$op] . ' ' . $this->escape( $op, $type, $value );

		if( in_array( $op, array( '=~', '~=' ), true ) ) {
			$term .= ' ESCAPE \'#\'';
		}

		return $term;
	}


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string|array $name Translated name(s) of the variable or column
	 * @param string $type Code of the internal value type
	 * @return string String that can be inserted into a SQL statement
	 */
	protected function createNullTerm( $name, string $type ) : string
	{
		switch( $this->getOperator() )
		{
			case '==': return $name . ' IS NULL';
			case '!=': return $name . ' IS NOT NULL';
		}

		throw new \Aimeos\MW\Common\Exception( sprintf( 'NULL value not allowed for operator "%1$s"', $this->getOperator() ) );
	}


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string|array $name Translated name(s) of the variable or column
	 * @param string $type Type constant
	 * @return string String that can be inserted into a SQL statement
	 */
	protected function createListTerm( $name, string $type ) : string
	{
		switch( $this->getOperator() )
		{
			case '==':
				return $name . ' IN ' . $this->createValueList( $type, (array) $this->getValue() );
			case '!=':
				return $name . ' NOT IN ' . $this->createValueList( $type, (array) $this->getValue() );
			default:
				$terms = [];

				foreach( (array) $this->getValue() as $val ) {
					$terms[] = $this->createTerm( $name, $type, $val );
				}

				return '(' . implode( ' OR ', $terms ) . ')';
		}
	}


	/**
	 * Creates a list of search values.
	 *
	 * @param string $type Type constant
	 * @param string[] $values Value list for the variable or column name
	 * @return string String of comma separated values in parenthesis
	 */
	protected function createValueList( string $type, array $values ) : string
	{
		if( empty( $values ) ) {
			return '(NULL)';
		}

		$operator = $this->getOperator();

		foreach( $values as $key => $value ) {
			$values[$key] = $this->escape( $operator, $type, $value );
		}

		return '(' . implode( ',', $values ) . ')';
	}


	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return double|string|int Escaped value
	 */
	protected function escape( string $operator, string $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case \Aimeos\MW\DB\Statement\Base::PARAM_NULL:
				$value = 'null'; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$value = (int) (bool) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$value = (int) (string) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$value = (double) (string) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
				if( $operator === '~=' ) {
					$value = '\'%' . str_replace( ['#', '%', '_', '['], ['##', '#%', '#_', '#['], $this->conn->escape( (string) $value ) ) . '%\''; break;
				}
				if( $operator === '=~' ) {
					$value = '\'' . str_replace( ['#', '%', '_', '['], ['##', '#%', '#_', '#['], $this->conn->escape( (string) $value ) ) . '%\''; break;
				}
			default: // all other operators: escape in default case
				$value = '\'' . $this->conn->escape( (string) $value ) . '\'';
		}

		return $value;
	}


	/**
	 * Returns the connection object.
	 *
	 * return \Aimeos\MW\DB\Connection\Iface Connection object
	 */
	public function getConnection() : \Aimeos\MW\DB\Connection\Iface
	{
		return $this->conn;
	}


	/**
	 * Returns the internal type of the function parameter.
	 *
	 * @param mixed &$item Reference to parameter value (will be updated if necessary)
	 * @return string Internal parameter type
	 * @throws \Aimeos\MW\Common\Exception If an error occurs
	 */
	protected function getParamType( &$item ) : string
	{
		if( is_null( $item ) ) {
			return \Aimeos\MW\DB\Statement\Base::PARAM_NULL;
		} elseif( is_float( $item ) ) {
			return \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT;
		} elseif( is_int( $item ) ) {
			return \Aimeos\MW\DB\Statement\Base::PARAM_INT;
		}

		return \Aimeos\MW\DB\Statement\Base::PARAM_STR;
	}
}
