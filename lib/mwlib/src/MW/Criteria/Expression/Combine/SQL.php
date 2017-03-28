<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
class SQL implements \Aimeos\MW\Criteria\Expression\Combine\Iface
{
	private static $operators = array( '&&' => 'AND', '||' => 'OR', '!' => 'NOT' );
	private $operator = '&&';
	private $expressions = [];


	/**
	 * Initializes the object.
	 *
	 * @param string $operator The used combine operator
	 * @param array $list List of expression objects
	 */
	public function __construct( $operator, array $list )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MW\\Criteria\\Expression\\Iface', $list );

		$this->operator = $operator;
		$this->expressions = $list;
	}


	/**
	 * Returns the list of expression objects that should be combined.
	 *
	 * @return array List of expression objects
	 */
	public function getExpressions()
	{
		return $this->expressions;
	}


	/**
	 * Returns the operator used for the expressions.
	 *
	 * @return string Operator used for the expressions
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
	 * Generates a string from the expression objects.
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = [], array $plugins = [] )
	{
		if( ( $item = reset( $this->expressions ) ) === false ) {
			return '';
		}

		$string = $item->toString( $types, $translations, $plugins );

		if( $this->operator == '!' && $string !== '' ) {
			return ' ' . self::$operators[$this->operator] . ' ' . $string;
		}

		while( ( $item = next( $this->expressions ) ) !== false )
		{
			if( ( $itemstr = $item->toString( $types, $translations, $plugins ) ) !== '' )
			{
				if( $string !== '' ) {
					$string .= ' ' . self::$operators[$this->operator] . ' ' . $itemstr;
				} else {
					$string = $itemstr;
				}
			}
		}

		return '( ' . $string . ' )';
	}


	/**
	 * Translates the sort key into the name required by the storage
	 *
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @return string|null Translated name (with replaced parameters if the name is an expression function)
	 */
	public function translate( array $translations )
	{
	}
}
