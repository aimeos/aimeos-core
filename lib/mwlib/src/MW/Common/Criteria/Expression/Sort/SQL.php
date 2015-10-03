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
	extends MW_Common_Criteria_Expression_Base
	implements MW_Common_Criteria_Expression_Sort_Interface
{
	private static $operators = array( '+' => 'ASC', '-' => 'DESC' );
	private $operator;
	private $conn;
	private $name;


	/**
	 * Initializes the object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection object
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable or column to sort
	 */
	public function __construct( MW_DB_Connection_Interface $conn, $operator, $name )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		$this->operator = $operator;
		$this->conn = $conn;
		$this->name = $name;
	}


	/**
	 * Returns the sorting direction operator.
	 *
	 * @return string Sorting direction ("+": ascending, "-": descending)
	 */
	public function getOperator()
	{
		return $this->operator;
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
	 * Returns the name of the variable or column to sort.
	 *
	 * @return string Name of the variable or column to sort
	 */
	public function getName()
	{
		return $this->name;
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
		$this->setPlugins( $plugins );

		$name = $this->name;

		if( ( $transname = $this->translateName( $name, $translations ) ) === '' ) {
			return '';
		}

		if( !isset( $types[$name] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid name "%1$s"', $name ) );
		}

		return $transname . ' ' . self::$operators[$this->operator];
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
			case MW_DB_Statement_Base::PARAM_BOOL:
				$value = (bool) $value; break;
			case MW_DB_Statement_Base::PARAM_INT:
				$value = (int) $value; break;
			case MW_DB_Statement_Base::PARAM_FLOAT:
				$value = (float) $value; break;
			case MW_DB_Statement_Base::PARAM_STR:
				if( $operator == '~=' ) {
					$value = '\'%' . $this->conn->escape( $value ) . '%\''; break;
				}
			default:
				$value = '\'' . $this->conn->escape( $value ) . '\'';
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
	protected function getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new MW_Common_Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
			}

			return MW_DB_Statement_Base::PARAM_STR;
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return MW_DB_Statement_Base::PARAM_FLOAT;
		}
		else if( ctype_digit( $item ) !== false )
		{
			return MW_DB_Statement_Base::PARAM_INT;
		}

		return MW_DB_Statement_Base::PARAM_STR;
	}
}
