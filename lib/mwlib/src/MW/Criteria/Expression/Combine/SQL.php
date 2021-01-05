<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Combine;


/**
 * SQL implementation for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
class SQL extends Base
{
	private static $operators = array( '&&' => 'AND', '||' => 'OR', '!' => 'NOT' );


	/**
	 * Initializes the object.
	 *
	 * @param string $operator The used combine operator
	 * @param array $list List of expression objects
	 */
	public function __construct( string $operator, array $list )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		parent::__construct( $operator, $list );
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
	 * Generates a string from the expression objects.
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of item names as keys and plugins objects as values
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return mixed Expression that evaluates to a boolean result
	 */
	public function toSource( array $types, array $translations = [], array $plugins = [], array $funcs = [] )
	{
		$expr = $this->getExpressions();

		if( ( $item = reset( $expr ) ) === false ) {
			return '';
		}

		$op = $this->getOperator();
		$string = $item->toSource( $types, $translations, $plugins, $funcs );

		if( $op == '!' && $string !== '' && $string !== null ) {
			return ' ' . self::$operators[$op] . ' ( ' . $string . ' )';
		}

		while( ( $item = next( $expr ) ) !== false )
		{
			$itemstr = $item->toSource( $types, $translations, $plugins, $funcs );

			if( $itemstr !== '' && $itemstr !== null )
			{
				if( $string !== '' && $string !== null ) {
					$string .= ' ' . self::$operators[$op] . ' ' . $itemstr;
				} else {
					$string = $itemstr;
				}
			}
		}

		return $string ? '( ' . $string . ' )' : '';
	}


	/**
	 * Translates the sort key into the name required by the storage
	 *
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return string|null Translated name (with replaced parameters if the name is an expression function)
	 */
	public function translate( array $translations, array $funcs = [] ) : ?string
	{
		return null;
	}
}
