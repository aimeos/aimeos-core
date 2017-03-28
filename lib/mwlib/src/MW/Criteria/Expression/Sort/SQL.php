<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Sort;


/**
 * SQL implementation for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
class SQL
	extends \Aimeos\MW\Criteria\Expression\Base
	implements \Aimeos\MW\Criteria\Expression\Sort\Iface
{
	private static $operators = array( '+' => 'ASC', '-' => 'DESC' );
	private $operator;
	private $conn;
	private $name;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection object
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable or column to sort
	 */
	public function __construct( \Aimeos\MW\DB\Connection\Iface $conn, $operator, $name )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
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
	 * @param array $plugins Associative list of item names and plugins implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = [], array $plugins = [] )
	{
		$this->setPlugins( $plugins );

		$name = $this->name;

		if( ( $transname = $this->translateName( $name, $translations ) ) === '' ) {
			return '';
		}

		if( !isset( $types[$name] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid name "%1$s"', $name ) );
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
			case \Aimeos\MW\DB\Statement\Base::PARAM_BOOL:
				$value = (bool) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_INT:
				$value = (int) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT:
				$value = (float) $value; break;
			case \Aimeos\MW\DB\Statement\Base::PARAM_STR:
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
