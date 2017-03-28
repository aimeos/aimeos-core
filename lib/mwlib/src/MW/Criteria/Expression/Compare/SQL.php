<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
class SQL extends \Aimeos\MW\Criteria\Expression\Compare\Base
{
	private static $operators = array( '=~' => 'LIKE', '~=' => 'LIKE', '==' => '=', '!=' => '<>', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', '&' => '&', '|' => '|' );
	private $conn = null;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable or column that should be compared.
	 * @param mixed $value Value that the variable or column should be compared to
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, $operator, $name, $value )
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
	public static function getOperators()
	{
		return array_keys( self::$operators );
	}


	/**
	 * Creates a term string from the given parameters.
	 *
	 * @param string $name Translated name of variable or column that should be compared
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return string Created term string (name operator value)
	 */
	protected function createTerm( $name, $type, $value )
	{
		$term = $name . ' ' . self::$operators[$this->getOperator()] . ' ' . $this->escape( $this->getOperator(), $type, $value );

		if( in_array( $this->getOperator(), array( '=~', '~=' ), true ) ) {
			$term .= ' ESCAPE \'#\'';
		}

		return $term;
	}


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string $name Translated name of the variable or column
	 * @return string String that can be inserted into a SQL statement
	 */
	protected function createNullTerm( $name )
	{
		switch( $this->getOperator() )
		{
			case '==':
				return $name . ' IS NULL';
			case '!=':
				return $name . ' IS NOT NULL';
			default:
				throw new \Aimeos\MW\Common\Exception( sprintf( 'NULL value not allowed for operator "%1$s"', $this->getOperator() ) );
		}
	}


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string $name Translated name of the variable or column
	 * @param integer $type Type constant
	 * @return string String that can be inserted into a SQL statement
	 */
	protected function createListTerm( $name, $type )
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
	 * @param integer $type Type constant
	 * @param string[] $values Value list for the variable or column name
	 * @return string String of comma separated values in parenthesis
	 */
	protected function createValueList( $type, array $values )
	{
		if( empty( $values ) ) {
			return '(NULL)';
		}

		$operator = $this->getOperator();

		foreach( $values as $key => $value ) {
			$values[$key] = $this->escape( $operator, $type, $value );
		}

		return '(' . implode(',', $values) . ')';
	}


	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return double|string|integer Escaped value
	 */
	protected function escape( $operator, $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$value = (int) (bool) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$value = (int) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$value = (double) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
				if( in_array( $operator, array( '~=', '=~' ), true ) )
				{
					$value = str_replace( array( '%', '_', '[' ), array( '#%', '#_', '#[' ), $this->conn->escape( $value ) );
					$value = '\'%' . $value . '%\''; break;
				}
			default:
				$value = '\'' . $this->conn->escape( $value ) . '\'';
		}

		return $value;
	}


	/**
	 * Returns the internal type of the function parameter.
	 *
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 * @return integer Internal parameter type
	 * @throws \Aimeos\MW\Common\Exception If an error occurs
	 */
	protected function getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
			}

			return \Aimeos\MW\DB\Statement\Base::PARAM_STR;
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT;
		}
		else if( ctype_digit( $item ) !== false )
		{
			return \Aimeos\MW\DB\Statement\Base::PARAM_INT;
		}

		return \Aimeos\MW\DB\Statement\Base::PARAM_STR;
	}
}
