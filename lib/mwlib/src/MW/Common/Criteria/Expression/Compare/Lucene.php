<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * PHP implementation for comparing objects.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Expression_Compare_Lucene extends MW_Common_Criteria_Expression_Compare_Abstract
{
	private static $_operators = array( '==' => true, '!=' => false, '~=' => true, '>=' => true, '<=' => true, '>' => false, '<' => false );


	/**
	 * Initializes the object.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection object
	 * @param string $operator Operator used for the expression
	 * @param string $name Name of variable or column that should be compared.
	 * @param mixed $value Value that the variable or column should be compared to
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
	 * Creates a Lucene query object from the given parameters.
	 *
	 * @param string $name Translated name of variable that should be compared
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable should be compared to
	 * @return Zend_Search_Lucene_Search_Query Lucene query object
	 */
	protected function _createTerm( $name, $type, $value )
	{
		switch( $this->getOperator() )
		{
			case '==':

				$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
				$term = new Zend_Search_Lucene_Index_Term( $this->_empty( $escaped ), $name );

				return new Zend_Search_Lucene_Search_Query_Term( $term );

			case '!=':

				$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
				$term = new Zend_Search_Lucene_Index_Term( $this->_empty( $escaped ), $name );

				return new Zend_Search_Lucene_Search_Query_MultiTerm( array( $term ), array( false ) );

			case '~=':

				if( ( $parts = explode( ' ', $value ) ) === false ) {
					throw new MW_Common_Exception( 'Empty term is not allowed for wildcard queries' );
				}

				$query = new Zend_Search_Lucene_Search_Query_Boolean();

				foreach( $parts as $part )
				{
					$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $part );
					$term = new Zend_Search_Lucene_Index_Term( strtolower( $this->_empty( $escaped ) ) . '*', $name );
					$query->addSubquery( new Zend_Search_Lucene_Search_Query_Wildcard( $term ) );
				}

				return $query;

			case '>=':
			case '>':

				$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
				$term = new Zend_Search_Lucene_Index_Term( $this->_empty( $escaped ), $name );
				$inclusive = self::$_operators[$this->getOperator()];

				return new Zend_Search_Lucene_Search_Query_Range( $term, null, $inclusive );

			case '<=':
			case '<':

				$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
				$term = new Zend_Search_Lucene_Index_Term( $this->_empty( $escaped ), $name );
				$inclusive = self::$_operators[$this->getOperator()];

				return new Zend_Search_Lucene_Search_Query_Range( null, $term, $inclusive );
		}
	}


	/**
	 * Creates a term which contains a null value.
	 *
	 * @param string $name Translated name of the variable or column
	 * @return string String that can be inserted into a SQL statement
	 */
	protected function _createNullTerm( $name )
	{
		return $this->_createTerm( $name, null, null );
	}


	/**
	 * Creates a Lucene query object from a list of values.
	 *
	 * @param string $name Translated name of the variable or column
	 * @param integer $type Type constant
	 * @return Zend_Search_Lucene_Search_Query Lucene query object
	 */
	protected function _createListTerm( $name, $type )
	{
		$sign = null;

		switch( $this->getOperator() )
		{
			case '!=':
				$sign = false;

			case '==':

				$multiterm = new Zend_Search_Lucene_Search_Query_MultiTerm();

				foreach( $this->getValue() as $value )
				{
					$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
					$term = new Zend_Search_Lucene_Index_Term( $this->_empty( $escaped ), $name );
					$multiterm->addTerm( $term, $sign );
				}

				return $multiterm;

			case '~=':

				$query = new Zend_Search_Lucene_Search_Query_Boolean();

				foreach( $this->getValue() as $value )
				{
					$escaped = $this->_escape( $this->getOperator(), SORT_STRING, $value );
					$term = new Zend_Search_Lucene_Index_Term( strtolower( $this->_empty( $escaped ) ) . '*', $name );
					$query->addSubquery( new Zend_Search_Lucene_Search_Query_Wildcard( $term ) );
				}

				return $query;
		}
	}


	protected function _empty( $value )
	{
		if( $value === null || $value === '' ) {
			$value = '(empty)';
		}

		return $value;
	}

	/**
	 * Escapes the value so it can be inserted into a SQL statement
	 *
	 * @param string $operator Operator used for the expression
	 * @param integer $type Type constant
	 * @param mixed $value Value that the variable or column should be compared to
	 * @return string Escaped value
	 */
	protected function _escape( $operator, $type, $value )
	{
		return $this->_translateValue( $this->getName(), $value );
	}


	/**
	 * Returns the internal type of the function parameter.
	 *
	 * @param string &$item Reference to parameter value (will be updated if necessary)
	 * @return integer Internal parameter type
	 * @throws MW_Common_Exception If an error occurs
	 */
	protected function _getParamType( &$item )
	{
		if( $item[0] == '"' )
		{
			if( ( $item = substr( $item, 1, strlen( $item ) - 2 ) ) === false ) {
				throw new MW_Common_Exception( sprintf( 'Unable to extract string parameter from >%1$s<', $item ) );
			}

			return SORT_STRING;
		}
		else if( strpos( $item, '.' ) !== false )
		{
			return SORT_NUMERIC;
		}
		else if( ctype_digit( $item ) !== false )
		{
			return SORT_NUMERIC;
		}

		return SORT_STRING;
	}
}
