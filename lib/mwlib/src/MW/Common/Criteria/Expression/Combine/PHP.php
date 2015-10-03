<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * PHP implementation for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Combine_PHP implements MW_Common_Criteria_Expression_Combine_Iface
{
	private static $operators = array( '&&' => '&&', '||' => '||', '!' => '!' );
	private $operator = '&&';
	private $expressions = array();


	/**
	 * Initializes the object.
	 *
	 * @param string $operator Operator used for the expressions
	 * @param array $list List of expression objects
	 */
	public function __construct( $operator, array $list )
	{
		if( !isset( self::$operators[$operator] ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		MW_Common_Base::checkClassList( 'MW_Common_Criteria_Expression_Iface', $list );

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
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Iface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = array(), array $plugins = array() )
	{
		if( ( $item = reset( $this->expressions ) ) === false ) {
			return '';
		}

		if( $this->operator == '!' ) {
			return ' ' . self::$operators[$this->operator] . ' ' . $item->toString( $types, $translations, $plugins );
		}

		$string = $item->toString( $types, $translations, $plugins );

		while( ( $item = next( $this->expressions ) ) !== false )
		{
			if( ( $itemstr = $item->toString( $types, $translations, $plugins ) ) !== '' ) {
				$string .= ' ' . self::$operators[$this->operator] . ' ' . $itemstr;
			}
		}

		return '( ' . $string . ' )';
	}
}
