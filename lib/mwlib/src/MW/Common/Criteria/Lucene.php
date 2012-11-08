<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: Lucene.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Lucene search class
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Lucene extends MW_Common_Criteria_Abstract
{
	private $_conditions = null;
	private $_sortations = array();
	private $_sliceStart = 0;
	private $_sliceSize = 100;


	/**
	 * Creates a new combine expression.
	 *
	 * Available composition operators are:
	 * "&&": term1 AND term2
	 * "||": term1 OR term2
	 * "!": NOT term
	 *
	 * @param string $operator One of the known operators
	 * @param array $list List of expression objects that should be combined
	 * @return MW_Common_Criteria_Expression_Combine_Interface Combine expression object
	 */
	public function combine( $operator, array $list )
	{
		return new MW_Common_Criteria_Expression_Combine_Lucene( $operator, $list );
	}


	/**
	 * Creates a new compare expression.
	 *
	 * Available comparision operators are:
	 * "==": item EQUAL value
	 * "!=": item NOT EQUAL value
	 * "~=": item LIKE value
	 * ">=": item GREATER OR EQUAL value
	 * "<=": item SMALLER OR EQUAL value
	 * ">": item GREATER value
	 * "<": item SMALLER value
	 *
	 * @param string $operator One of the known operators
	 * @param string $name Name of the variable or column that should be used for comparison
	 * @param mixed $value Value the variable or column should be compared to
	 * @return MW_Common_Criteria_Expression_Compare_Interface Compare expression object
	 */
	public function compare( $operator, $name, $value )
	{
		return new MW_Common_Criteria_Expression_Compare_Lucene( $operator, $name, $value );
	}


	/**
	 * Creates a new sort expression.
	 *
	 * Available sorting operators are:
	 * "+": sort ascending
	 * "-": sort descending
	 *
	 * @param string $operator One of the known operators
	 * @param string $name Name of the variable or column that should be used for sorting
	 * @return MW_Common_Criteria_Expression_Sort_Interface Sort expression object
	 */
	public function sort( $operator, $name )
	{
		return new MW_Common_Criteria_Expression_Sort_Lucene( $operator, $name );
	}


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators()
	{
		return array(
			'combine' => MW_Common_Criteria_Expression_Combine_Lucene::getOperators(),
			'compare' => MW_Common_Criteria_Expression_Compare_Lucene::getOperators(),
			'sort' => MW_Common_Criteria_Expression_Sort_Lucene::getOperators(),
		);
	}


	/**
	 * Returns the expression string.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return string Expression string for searching
	 */
	public function getConditionString( array $types, array $translations = array(), array $plugins = array() )
	{
		if( $this->_conditions !== null ) {
			return $string = $this->_conditions->toString( $types, $translations, $plugins );
		}

		return new Zend_Search_Lucene_Search_Query_Insignificant();
	}


	/**
	 * Returns the original condition expression objects.
	 *
	 * @return MW_Common_Criteria_Expression_Interface Original expression objects
	 */
	public function getConditions()
	{
		return $this->_conditions;
	}


	/**
	 * Sets the expression objects.
	 *
	 * @param MW_Common_Criteria_Expression_Interface $conditions Expression object
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setConditions( MW_Common_Criteria_Expression_Interface $conditions )
	{
		if( $conditions instanceof MW_Common_Criteria_Expression_Sort_Interface ) {
			throw new MW_Common_Exception( 'Sortation objects are not allowed' );
		}

		$this->_conditions = $conditions;
		return $this;
	}


	/**
	 * Returns the string for sorting the result
	 *
	 * @param array $names List of item names
	 * @param array $translations Associative list of item names that should be translated
	 * @return string Order string for sorting the items
	 */
	public function getSortationString( array $types, array $translations = array() )
	{
		$sortation = '';

		foreach( $this->_sortations as $sortitem )
		{
			if( ( $string = $sortitem->toString( $types, $translations ) ) !== '' ) {
				$sortation .= $string;
			}
		}

		return $sortation;
	}


	/**
	 * Returns the original sorting array for ordering the results.
	 *
	 * @return array Original sortation list (array of objects)
	 */
	public function getSortations()
	{
		return $this->_sortations;
	}


	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param array $sortation List of objects implementing MW_Common_Criteria_Expression_Sort_Interface
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setSortations( array $sortations )
	{
		MW_Common_Abstract::checkClassList( 'MW_Common_Criteria_Expression_Sort_Lucene', $sortations );

		$this->_sortations = $sortations;
		return $this;
	}


	/**
	 * Returns the number of requested items.
	 *
	 * @return integer Number of items
	 */
	public function getSliceSize()
	{
		return $this->_sliceSize;
	}


	/**
	 * Returns the start number of requested items.
	 *
	 * @return integer Start number of the items
	 */
	public function getSliceStart()
	{
		return $this->_sliceStart;
	}


	/**
	* Sets the start number and the size of the requested data slice.
	*
	* @param integer Start number of the items
	* @param integer Number of items
	* @return MW_Common_Criteria_SQL Object instance for fluent interface
	*/
	public function setSlice( $start, $size = 100 )
	{
		$this->_sliceStart = (int) $start;
		$this->_sliceSize = (int) $size;

		return $this;
	}
}
