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
 * PHP implementation for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
class PHP extends Base
{
	private static $operators = array( '&&' => '&&', '||' => '||', '!' => '!' );
	private $expressions = [];
	private $operator;


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Operator used for the expressions
	 * @param \Aimeos\MW\Criteria\Expression\Iface[] $list List of expression objects
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
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of item names as keys and plugin objects as values
	 * @return mixed Expression that evaluates to a boolean result
	 */
	public function toSource( array $types, array $translations = [], array $plugins = [], array $funcs = [] )
	{
		$expr = $this->getExpressions();

		if( ( $item = reset( $expr ) ) === false ) {
			return '';
		}

		$op = $this->getOperator();

		if( $op == '!' ) {
			return ' ' . self::$operators[$op] . ' ' . $item->toSource( $types, $translations, $plugins );
		}

		$string = $item->toSource( $types, $translations, $plugins, $funcs );

		while( ( $item = next( $expr ) ) !== false )
		{
			if( ( $itemstr = $item->toSource( $types, $translations, $plugins ) ) !== '' ) {
				$string .= ' ' . self::$operators[$op] . ' ' . $itemstr;
			}
		}

		return '( ' . $string . ' )';
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
