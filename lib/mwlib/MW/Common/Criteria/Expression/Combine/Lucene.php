<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id
 */


/**
 * Lucene implementation for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Combine_Lucene implements MW_Common_Criteria_Expression_Combine_Interface
{
	private static $_operators = array( '&&' => true, '||' => null, '!' => false );
	private $_operator = '&&';
	private $_expressions = array();


	/**
	 * Initializes the object.
	 *
	 * @param array $list List of expression objects
	 */
	public function __construct( $operator, array $list )
	{
		if( !array_key_exists( $operator, self::$_operators ) ) {
			throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $operator ) );
		}

		MW_Common_Abstract::checkClassList( 'MW_Common_Criteria_Expression_Interface', $list );

		$this->_operator = $operator;
		$this->_expressions = $list;
	}


	/**
	 * Returns the list of expression objects that should be combined.
	 *
	 * @return array List of expression objects
	 */
	public function getExpressions()
	{
		return $this->_expressions;
	}


	/**
	 * Returns the operator used for the expressions.
	 *
	 * @return string Operator used for the expressions
	 */
	public function getOperator()
	{
		return $this->_operator;
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
	 * Generates a Lucene object from the expression objects.
	 *
	 * @param array $names Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return Zend_Search_Lucene_Search_Query_MultiTerm Combined search objects
	 */
	public function toString( array $types, array $translations = array(), array $plugins = array() )
	{
		$query = new Zend_Search_Lucene_Search_Query_Boolean();

		foreach( $this->_expressions as $expr )
		{
			if( ( $itemstr = $expr->toString( $types, $translations, $plugins ) ) !== '' ) {
				$query->addSubquery( $itemstr, self::$_operators[$this->_operator] );
			}
		}

		return $query;
	}
}
