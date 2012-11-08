<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: PHP.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * PHP implementation for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Sort_PHP
	extends MW_Common_Criteria_Expression_Abstract
	implements MW_Common_Criteria_Expression_Sort_Interface
{
	private static $_operators = array( '+' => 'asort', '-' => 'arsort' );
	private $_operator = '+';


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable to sort
	 */
	public function __construct( $operator, $name )
	{
		if( !isset( self::$_operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		$this->_operator = $operator;
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
	 * Returns the name of the variable to sort.
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
	 * @param array $names Associative list of variable or column names as keys and their corresponding types
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

		return self::$_operators[$this->_operator] . '(' . $transname . ');';
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
			case '(float)':
				return (float) $value;
			case '(int)':
				return (int) $value;
			default:
				return addcslashes( $value, '\'"' );
		}
	}


	/**
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 *
	 * @param string $item Parameter value
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

			return '(string)';
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return '(float)';
		}
		else if( ctype_digit( $item ) !== false )
		{
			return '(int)';
		}

		return '(string)';
	}
}
