<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * SQL implementation for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Compare_SQL extends MW_Common_Criteria_Expression_Compare_Abstract
{
	private static $operators = array( '==' => '=', '!=' => '<>', '~=' => 'LIKE', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<', '&' => '&', '|' => '|', '=~' => 'LIKE' );
	private $conn = null;


	/**
	 * Initializes the object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection object
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable or column that should be compared.
	 * @param mixed $value Value that the variable or column should be compared to
	 */
	public function __construct( MW_DB_Connection_Interface $conn, $operator, $name, $value )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
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
		return $name . ' ' . self::$operators[$this->getOperator()] . ' ' . $this->escape( $this->getOperator(), $type, $value );
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
				throw new MW_Common_Exception( sprintf( 'NULL value not allowed for operator "%1$s"', $this->getOperator() ) );
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
				$terms = array();

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
	 * @return string Escaped value
	 */
	protected function escape( $operator, $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case MW_DB_Statement_Abstract::PARAM_BOOL:
				$value = (int) (bool) $value; break;
			case MW_DB_Statement_Abstract::PARAM_INT:
				$value = (int) $value; break;
			case MW_DB_Statement_Abstract::PARAM_FLOAT:
				$value = (float) $value; break;
			case MW_DB_Statement_Abstract::PARAM_STR:
				if( $operator == '~=' ) {
					$value = '\'%' . $this->conn->escape( $value ) . '%\''; break;
				}
				if( $operator == '=~' ) {
					$value = '\'' . $this->conn->escape( $value ) . '%\''; break;
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
	 * @throws MW_Common_Exception If an error occurs
	 */
	protected function getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new MW_Common_Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
			}

			return MW_DB_Statement_Abstract::PARAM_STR;
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return MW_DB_Statement_Abstract::PARAM_FLOAT;
		}
		else if( ctype_digit( $item ) !== false )
		{
			return MW_DB_Statement_Abstract::PARAM_INT;
		}

		return MW_DB_Statement_Abstract::PARAM_STR;
	}
}
