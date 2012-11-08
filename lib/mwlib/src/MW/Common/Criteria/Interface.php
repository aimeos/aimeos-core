<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Interface for search classes
 *
 * @package MW
 * @subpackage Common
 */
interface MW_Common_Criteria_Interface
{
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
	public function combine( $operator, array $list );


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
	public function compare( $operator, $name, $value );


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
	public function sort( $operator, $name );


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators();


	/**
	 * Returns the expression string.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return string Expression string for searching
	 */
	public function getConditionString( array $types, array $translations = array(), array $plugins = array() );


	/**
	 * Returns the original condition expression objects.
	 *
	 * @return MW_Common_Criteria_Expression_Interface Original expression objects
	 */
	public function getConditions();


	/**
	 * Sets the expression objects.
	 *
	 * @param MW_Common_Criteria_Expression_Interface $conditions Expression object
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setConditions( MW_Common_Criteria_Expression_Interface $conditions );


	/**
	 * Returns the string for sorting the result
	 *
	 * @param array $names List of item names
	 * @param array $translations Associative list of item names that should be translated
	 * @return string Order string for sorting the items
	 */
	public function getSortationString( array $names, array $translations = array() );


	/**
	 * Returns the original sorting array for ordering the results.
	 *
	 * @return array Original sortation array
	 */
	public function getSortations();


	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param array $sortation List of objects implementing MW_Common_Criteria_Expression_Sort_Interface
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setSortations( array $sortation );


	/**
	 * Returns the start number of requested items.
	 *
	 * @return integer Start number of the items
	 */
	public function getSliceStart();


	/**
	 * Returns the number of requested items.
	 *
	 * @return integer Number of items
	 */
	public function getSliceSize();


	/**
	 * Sets the start number and the size of the requested data slice.
	 *
	 * @param integer Start number of the items
	 * @param integer Number of items
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setSlice( $start, $size = 100 );


	/**
	 * Creates condition expressions from a multi-dimensional associative array.
	 *
	 * The simplest form of a valid associative array is a single comparison:
	 * 	$array = array(
	 * 		'==' => array( 'name' => 'value' ),
	 * 	);
	 *
	 * Combining several conditions can look like:
	 * 	$array = array(
	 * 		'&&' => array(
	 * 			0 => array(
	 * 				'==' => array( 'name' => 'value' ),
	 * 			1 => array(
	 * 				'==' => array( 'name2' => 'value2' ),
	 * 			),
	 * 		),
	 * 	);
	 *
	 * Nested combine operators are also possible.
	 *
	 * @param array $array Multi-dimensional associative array containing the expression arrays
	 * @return MW_Common_Criteria_Expression_Interface Condition expressions (maybe nested)
	 * @throws MW_Common_Exception If given array is invalid
	 */
	public function toConditions( array $array );


	/**
	 * Creates sortation expressions from an associative array.
	 *
	 * The array must be a single-dimensional array of name and operator pairs like
	 * 	$array = array(
	 * 		'name' => '+',
	 * 		'name2' => '-',
	 * 	);
	 *
	 * @param array $array Single-dimensional array of name and operator pairs
	 * @return array List of sort expressions implementing MW_Common_Criteria_Expression_Sort_Interface
	 */
	public function toSortations( array $array );
}
