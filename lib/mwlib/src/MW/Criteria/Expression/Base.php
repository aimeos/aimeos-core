<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression;


/**
 * Abstract expression class with basic methods
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base
{
	private $plugins = [];


	/**
	 * Translates the sort key into the name required by the storage
	 *
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @return string Translated name (with replaced parameters if the name is an expression function)
	 */
	public function translate( array $translations )
	{
		$name = $this->getName();
		return $this->translateName( $name, $translations );
	}


	/**
	 * Creates a function signature for compare expressions.
	 *
	 * @param string $name Function name
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, etc.
	 * @return string Function signature
	 */
	public static function createFunction( $name, array $params )
	{
		return $name . '(' . self::createSignature( $params ) . ')';
	}


	/**
	 * Creates a parameter signature for compare expressions.
	 *
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, etc.
	 * @return string Parameter signature
	 */
	protected static function createSignature( array $params )
	{
		$list = [];

		foreach( $params as $param )
		{
			if( $param === null ) {
				$list[] = 'null'; continue;
			}

			switch( gettype( $param ) )
			{
				case 'boolean':
				case 'integer':
					$list[] = (int) $param; break;
				case 'double':
					$list[] = (double) $param; break;
				case 'array':
					$list[] = '[' . self::createSignature( $param ) . ']'; break;
				default:
					$list[] = '"' . str_replace( ['"', '[', ']'], ' ', $param ) . '"';
			}
		}

		return implode( ',', $list );
	}


	/**
	 * Checks if the given string is an expression function and returns the parameters.
	 * The parameters will be cut off the function name and will be added to
	 * the given parameter array
	 *
	 * @param string $name Function string to check, will be cut to <function>() (without parameter)
	 * @param array $params Array that will contain the list of parameters afterwards
	 * @return boolean True if string is an expression function, false if not
	 */
	protected function isFunction( &$name, array &$params )
	{
		$len = strlen( $name );
		if( $len === 0 || $name[$len-1] !== ')' ) { return false; }

		if( ( $pos = strpos( $name, '(' ) ) === false ) {
			throw new \Aimeos\MW\Common\Exception( 'Missing opening bracket for function syntax' );
		}

		if( ( $paramstr = substr( $name, $pos, $len - $pos ) ) === false ) {
			throw new \Aimeos\MW\Common\Exception( 'Unable to extract function parameter' );
		}

		if( ( $namestr = substr( $name, 0, $pos ) ) === false ) {
			throw new \Aimeos\MW\Common\Exception( 'Unable to extract function name' );
		}

		$matches = [];
		$pattern = '/(\[[^\]]*\]|"[^"]*"|[0-9]+\.[0-9]+|[0-9]+|null),?/';

		if( preg_match_all( $pattern, $paramstr, $matches ) === false ) {
			throw new \Aimeos\MW\Common\Exception( 'Unable to extract function parameters' );
		}

		if( isset( $matches[1] ) ) {
			$params = $this->extractParams( $matches[1] );
		}

		$name = $namestr . '()';
		return true;
	}


	/**
	 * @deprecated Removed 2019.01
	 */
	public function toString( array $types, array $translations = [], array $plugins = [] )
	{
		return $this->toSource( $types, $translations, $plugins );
	}


	/**
	 * Replaces the parameters in nested arrays
	 *
	 * @param array $list Multi-dimensional associative array of values including positional parameter, e.g. "$1"
	 * @param array $find List of strings to search for, e.g. ['$1', '$2']
	 * @param array $replace List of strings to replace by, e.g. ['val1', 'val2']
	 * @return Multi-dimensional associative array with parameters replaced
	 */
	protected function replaceParameter( array $list, array $find, array $replace )
	{
		foreach( $list as $key => $value )
		{
			if( is_array( $value ) ) {
				$list[$key] = $this->replaceParameter( $value, $find, $replace );
			} else {
				$list[$key] = str_replace( $find, $replace, $value );
			}
		}

		return $list;
	}


	/**
	 * Translates an expression string and replaces the parameter if it's an expression function.
	 *
	 * @param string $name Expresion string or function
	 * @param array $translations Associative list of names and their translations (may include parameter if a name is an expression function)
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return string Translated name (with replaced parameters if the name is an expression function)
	 */
	protected function translateName( &$name, array $translations = [], array $funcs = [] )
	{
		$params = [];

		if( $this->isFunction( $name, $params ) === true )
		{
			$source = $name;
			if( isset( $translations[$name] ) ) {
				$source = $translations[$name];
			}

			if( isset( $funcs[$name] ) ) {
				$params = $funcs[$name]( $source, $params );
			}

			$find = [];
			$count = count( $params );

			for( $i = 0; $i < $count; $i++ ) {
				$find[$i] = '$' . ( $i + 1 );
			}

			if( is_array( $source ) ) {
				return $this->replaceParameter( $source, $find, $params );
			}

			return str_replace( $find, $params, $source );
		}

		if( isset( $translations[$name] ) ) {
			return $translations[$name];
		}

		return $name;
	}


	/**
	 * Translates a value to another one by a plugin if available.
	 *
	 * @param string $name Name of variable or column that should be translated
	 * @param mixed $value Original value
	 * @return string Translated value
	 */
	protected function translateValue( $name, $value )
	{
		if( isset( $this->plugins[$name] ) ) {
			return $this->plugins[$name]->translate( $value );
		}

		return $value;
	}


	/**
	 * Sets the new plugins for translating values.
	 *
	 * @param array $plugins Associative list of names and the plugin implementing \Aimeos\MW\Criteria\Plugin\Iface
	 */
	protected function setPlugins( array $plugins )
	{
		\Aimeos\MW\Common\Base::checkClassList('\\Aimeos\\MW\\Criteria\\Plugin\\Iface', $plugins);

		$this->plugins = $plugins;
	}


	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param integer $type Type constant
	 * @param string $value Value that the variable or column should be compared to
	 * @return string Escaped value
	 */
	abstract protected function escape( $operator, $type, $value );


	/**
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 *
	 * @param string $item Parameter value
	 * @return integer Internal parameter type
	 * @throws \Aimeos\MW\Common\Exception If an error occurs
	 */
	abstract protected function getParamType( &$item );


	/**
	 * Extracts the function parameters from the parameter strings.
	 *
	 * @param string[] $strings List of matched strings
	 * @return array List of found parameters
	 */
	protected function extractParams( array $strings )
	{
		$params = [];

		foreach( $strings as $string )
		{
			if( isset( $string[0] ) && $string[0] == '[' )
			{
				$items = [];
				$pattern = '/("[^"]*"|[0-9]+\.[0-9]+|[0-9]+|null),?/';

				if( preg_match_all( $pattern, $string, $items ) === false ) {
					throw new \Aimeos\MW\Common\Exception( 'Unable to extract function parameters' );
				}

				if( isset( $items[1] ) )
				{
					$list = [];

					foreach( $items[1] as $item ) {
						$list[] = $this->escape( '==', $this->getParamType( $item ), $item );
					}

					$params[] = implode( ',', $list );
				}
			}
			else
			{
				$params[] = $this->escape( '==', $this->getParamType( $string ), $string );
			}
		}

		return $params;
	}
}
