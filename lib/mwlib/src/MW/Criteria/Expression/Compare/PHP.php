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
 * PHP implementation for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
class PHP extends Base
{
	private static $operators = ['>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=', '==' => '==', '!=' => '!=', '-' => '-'];


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable that should be compared.
	 * @param mixed $value Value that the variable should be compared to
	 */
	public function __construct( string $operator, string $name, $value )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		parent::__construct( $operator, $name, $value );
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
	 * @param mixed $value Value that the variable should be compared to
	 * @return string Created term string (name operator value)
	 */
	protected function createTerm( $name, string $type, $value ) : string
	{
		$op = $this->getOperator();

		if( $op === '-' )
		{
			$parts = explode( ' - ', $value );

			return $name . ' >= ' . $this->escape( '>=', $type, $parts[0] )
				. ' && ' . $name . ' < ' . $this->escape( '<', $type, $parts[1] );
		}

		return $name . ' ' . self::$operators[$op] . ' ' . $this->escape( $op, $type, $value );
	}


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string|array $name Translated name(s) of the variable or column
	 * @param string $type Code of the internal value type
	 * @return string Created term string (name operator null)
	 */
	protected function createNullTerm( $name, string $type ) : string
	{
		if( is_array( $name ) ) {
			return '';
		}

		switch( $this->getOperator() )
		{
			case '==':
				return $name . ' === null';
			case '!=':
				return $name . ' !== null';
			default:
				throw new \Aimeos\MW\Common\Exception( sprintf( 'null value not allowed for operator "%1$s"', $this->getOperator() ) );
		}
	}


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string|array $name Translated name(s) of the variable or column
	 * @param string $type Type constant
	 * @return string Created term string
	 */
	protected function createListTerm( $name, string $type ) : string
	{
		switch( $this->getOperator() )
		{
			case '==':

				$list = [];
				foreach( (array) $this->getValue() as $value ) {
					$list[] = $this->createTerm( $name, $type, $value );
				}
				return '( ' . implode( ' || ', $list ) . ' )';

			case '!=':

				$list = [];
				foreach( (array) $this->getValue() as $value ) {
					$list[] = $this->createTerm( $name, $type, $value );
				}
				return '( ' . implode( ' && ', $list ) . ' )';

			default:
				throw new \Aimeos\MW\Common\Exception( sprintf( 'List Term not allowed for operator "%1$s"', $this->getOperator() ) );
		}
	}


	/**
	 * Creates a list of search values.
	 *
	 * @param string $type Type constant
	 * @param array $values Value list for the variable
	 * @return string array-String of comma separated values
	 */
	protected function createValueList( string $type, array $values ) : string
	{
		if( ( $val = reset( $values ) ) === false ) {
			return '';
		}

		$string = $this->escape( $this->getOperator(), $type, $val );

		while( ( $val = next( $values ) ) !== false ) {
			$string .= ',' . $this->escape( $this->getOperator(), $type, $val );
		}

		return 'array(' . $string . ')';
	}


	/**
	 * Escapes the value
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable should be compared to
	 * @return boolean|double|integer|string Escaped value
	 */
	protected function escape( string $operator, string $type, $value )
	{
		$value = $this->translateValue( $this->getName(), $value );

		switch( $type )
		{
			case 'null':
				$value = null; break;
			case 'bool':
				$value = (bool) $value; break;
			case 'int':
				$value = (int) $value; break;
			case 'float':
				$value = (double) $value; break;
			default:
				$value = '\'' . addcslashes( (string) $value, '\'' ) . '\'';
		}

		return $value;
	}


	/**
	 * Returns the parameter type.
	 *
	 * @param mixed &$item Parameter string to evaluate (double quotes will be removed if necessary)
	 * @return string Data type (string, float, int)
	 */
	protected function getParamType( &$item ) : string
	{
		if( is_null( $item ) ) {
			return 'null';
		} elseif( is_float( $item ) ) {
			return 'float';
		} elseif( is_int( $item ) ) {
			return 'int';
		}

		return 'string';
	}
}
