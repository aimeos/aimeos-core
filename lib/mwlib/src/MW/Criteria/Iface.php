<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria;


/**
 * Interface for search classes
 *
 * @package MW
 * @subpackage Common
 */
interface Iface
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
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
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
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
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
	 * @return \Aimeos\MW\Criteria\Expression\Sort\Iface Sort expression object
	 */
	public function sort( $operator, $name );


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators();

	/**
	 * Returns a translated colum list separated by comma
	 *
	 * @param array $columns List of column names or objects implementing getName() method
	 * @param array $translations Associative list of item names that should be translated
	 * @return string Translated columns
	 */
	public function getColumnString( array $columns, array $translations = [] );


	/**
	 * Returns the expression string.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return string Expression string for searching
	 */
	public function getConditionString( array $types, array $translations = [], array $plugins = [] );


	/**
	 * Returns the original condition expression objects.
	 *
	 * @return \Aimeos\MW\Criteria\Expression\Iface Original expression objects
	 */
	public function getConditions();


	/**
	 * Sets the expression objects.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $conditions Expression object
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setConditions( \Aimeos\MW\Criteria\Expression\Iface $conditions );


	/**
	 * Returns the string for sorting the result
	 *
	 * @param array $names List of item names
	 * @param array $translations Associative list of item names that should be translated
	 * @return string Order string for sorting the items
	 */
	public function getSortationString( array $names, array $translations = [] );


	/**
	 * Returns the original sorting array for ordering the results.
	 *
	 * @return array Original sortation array
	 */
	public function getSortations();


	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Sort\Iface[] $sortation List of objects implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
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
	 * @param integer $start Start number of the items
	 * @param integer $size Number of items
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
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
	 * @return \Aimeos\MW\Criteria\Expression\Iface Condition expressions (maybe nested)
	 * @throws \Aimeos\MW\Common\Exception If given array is invalid
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
	 * @return array List of sort expressions implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 */
	public function toSortations( array $array );
}
