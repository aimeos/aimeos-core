<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * SQL implementation for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Sort_SQL
	extends MW_Common_Criteria_Expression_Abstract
	implements MW_Common_Criteria_Expression_Sort_Interface
{
	private static $_operators = array( '+' => 'ASC', '-' => 'DESC' );
	private $_operator;
	private $_conn;
	private $_name;


	/**
	 * Initializes the object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection object
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable or column to sort
	 */
	public function __construct( MW_DB_Connection_Interface $conn, $operator, $name )
	{
		if( !isset( self::$_operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		$this->_operator = $operator;
		$this->_conn = $conn;
		$this->_name = $name;
	}


	/**
	 * Returns the sorting direction operator.
	 *
	 * @return string Sorting direction ("+": ascending, "-": descending)
	 */
	public function getOperator()
	{
		return $this->_operator;
	}


	/**
	 * Returns the available operators for the expression.
	 *
	 * @return array List of available operators
	 */
	public static function getOperators()
	{
		return array_keys( self::$_operators );
	}


	/**
	 * Returns the name of the variable or column to sort.
	 *
	 * @return string Name of the variable or column to sort
	 */
	public function getName()
	{
		return $this->_name;
	}


	/**
	 * Generates a string from the expression objects.
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = array(), array $plugins = array() )
	{
		$this->_setPlugins( $plugins );

		$name = $this->_name;

		if( ( $transname = $this->_translateName( $name, $translations ) ) === '' ) {
			return '';
		}

		if( !isset( $types[$name] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid name "%1$s"', $name ) );
		}

		return $transname . ' ' . self::$_operators[$this->_operator];
	}


	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return string Escaped value
	 */
	protected function _escape( $operator, $type, $value )
	{
		$value = $this->_translateValue( $this->getName(), $value );

		switch( $type )
		{
			case MW_DB_Statement_Abstract::PARAM_BOOL:
				$value = (bool) $value; break;
			case MW_DB_Statement_Abstract::PARAM_INT:
				$value = (int) $value; break;
			case MW_DB_Statement_Abstract::PARAM_FLOAT:
				$value = (float) $value; break;
			case MW_DB_Statement_Abstract::PARAM_STR:
				if( $operator == '~=' ) {
					$value = '\'%' . $this->_conn->escape( $value ) . '%\''; break;
				}
			default:
				$value = '\'' . $this->_conn->escape( $value ) . '\'';
		}

		return (string) $value;
	}


	/**
	 * Returns the internal type of the function parameter.
	 *
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 * @return integer Internal parameter type
	 * @throws MW_Common_Exception If an error occurs
	 */
	protected function _getParamType( &$item )
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
