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
 * PHP implementation for sorting objects.
 *
 * @package MW
 * @subpackage Common
 */
class PHP
	extends \Aimeos\MW\Criteria\Expression\Base
	implements \Aimeos\MW\Criteria\Expression\Sort\Iface
{
	private static $operators = array( '+' => 'asort', '-' => 'arsort' );
	private $operator;
	private $name;


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Sorting operator ("+": ascending, "-": descending)
	 * @param string $name Name of the variable to sort
	 */
	public function __construct( $operator, $name )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		$this->operator = $operator;
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
	 * Returns the name of the variable to sort.
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

		return self::$operators[$this->operator] . '(' . $transname . ');';
	}


	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return double|integer|string Escaped value
	 */
	protected function escape( $operator, $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case '(float)':
				return (double) $value;
			case '(int)':
				return (int) $value;
			default:
				return addcslashes( $value, '\'"' );
		}
	}


	/**
	 * Returns the internal parameter type for the given string
	 *
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 * @return string Internal parameter type like string, float or int
	 * @throws \Aimeos\MW\Common\Exception If an error occurs
	 */
	protected function getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
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
