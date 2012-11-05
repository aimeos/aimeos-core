<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: PHP.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * PHP implementation for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Compare_PHP extends MW_Common_Criteria_Expression_Compare_Abstract
{
	private static $_operators = array( '==' => '==', '!=' => '!=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<' );


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable that should be compared.
	 * @param mixed $value Value that the variable should be compared to
	 */
	public function __construct( $operator, $name, $value )
	{
		if( !isset( self::$_operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		parent::__construct( $operator, $name, $value );
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
	 * Creates a term string from the given parameters.
	 *
	 * @param string $name Translated name of variable that should be compared
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable should be compared to
	 * @return string Created term string (name operator value)
	 */
	protected function _createTerm( $name, $type, $value )
	{
		$escaped = $this->_escape( $this->getOperator(), $type, $value );
		return $name . ' ' . self::$_operators[$this->getOperator()] . ' ' . $escaped;
	}


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string $name Translated name of the variable or column
	 * @return string Created term string (name operator null)
	 */
	protected function _createNullTerm( $name )
	{
		switch( $this->getOperator() )
		{
			case '==':
				return $name . ' === null';
			case '!=':
				return $name . ' !== null';
			default:
				throw new MW_Common_Exception( sprintf( 'null value not allowed for operator "%1$s"', $this->getOperator() ) );
		}
	}


	/**
	 * Creates a term from a list of values.
	 *
	 * @param string $name Translated name of the variable
	 * @param string $type Type constant
	 * @return string Created term string
	 */
	protected function _createListTerm( $name, $type )
	{
		switch( $this->getOperator() )
		{
			case '==':

				$list = array();
				foreach( $this->getValue() as $value ) {
					$list[] = $this->_createTerm( $name, $type, $value );
				}
				return '( ' . implode( ' || ', $list ) . ' )';

			case '!=':

				$list = array();
				foreach( $this->getValue() as $value ) {
					$list[] = $this->_createTerm( $name, $type, $value );
				}
				return '( ' . implode( ' && ', $list ) . ' )';

			default:
				throw new MW_Common_Exception( sprintf( 'List Term not allowed for operator "%1$s"', $this->getOperator() ) );
		}
	}


	/**
	 * Creates a list of search values.
	 *
	 * @param string $type Type constant
	 * @param array $values Value list for the variable
	 * @return string array-String of comma separated values
	 */
	protected function _createValueList( $type, array $values )
	{
		if( ( $val = reset( $values ) ) === false ) {
			return '';
		}

		$string = $this->_escape( $this->getOperator(), $type, $val );

		while( ( $val = next( $values ) ) !== false ) {
			$string .= ',' . $this->_escape( $this->getOperator(), $type, $val );
		}

		return 'array(' . $string . ')';
	}


	/**
	 * Escapes the value
	 *
	 * @param string $operator Operator used for the expression
	 * @param string $type Type constant
	 * @param mixed $value Value that the variable should be compared to
	 * @return mixed Escaped value
	 */
	protected function _escape( $operator, $type, $value )
	{
		$value = $this->_translateValue( $this->getName(), $value );

		switch( $type )
		{
			case 'bool':
				$value = (bool) $value; break;
			case 'int':
				$value = (int) $value; break;
			case 'float':
				$value = (float) $value; break;
			default:
				$value = '\'' . addcslashes( $value, '\'' ) . '\'';
		}

		return $value;
	}


	/**
	 * Get the parameter type
	 *
	 * @param string $item
	 * @return parameter type
	 */
	protected function _getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new MW_Common_Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
			}

			return 'string';
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return 'float';
		}
		else if( ctype_digit( $item ) !== false )
		{
			return 'int';
		}

		return 'string';
	}
}
