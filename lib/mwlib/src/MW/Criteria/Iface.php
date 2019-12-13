<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @param \Aimeos\MW\Criteria\Expression\Compare\Iface[] $list List of expression objects
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function combine( string $operator, array $list ) : \Aimeos\MW\Criteria\Expression\Combine\Iface;


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
	public function compare( string $operator, string $name, $value ) : \Aimeos\MW\Criteria\Expression\Compare\Iface;


	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 * @return string Function signature
	 */
	public function createFunction( string $name, array $params ) : string;


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
	public function sort( string $operator, string $name ) : \Aimeos\MW\Criteria\Expression\Sort\Iface;


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators() : array;


	/**
	 * Returns the expression string.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param \Aimeos\MW\Criteria\Plugin\Iface[] $plugins Associative list of item names as keys and plugin objects as values
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return mixed Data for searching
	 */
	public function getConditionSource( array $types, array $translations = [], array $plugins = [], array $funcs = [] );


	/**
	 * Returns the original condition expression objects.
	 *
	 * @return \Aimeos\MW\Criteria\Expression\Iface|null Original expression objects
	 */
	public function getConditions() : ?\Aimeos\MW\Criteria\Expression\Iface;


	/**
	 * Sets the expression objects.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $conditions Expression object
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setConditions( \Aimeos\MW\Criteria\Expression\Iface $conditions ) : Iface;


	/**
	 * Returns the string for sorting the result
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return mixed Data for sorting the items
	 */
	public function getSortationSource( array $types, array $translations = [], array $funcs = [] );


	/**
	 * Returns the original sorting array for ordering the results.
	 *
	 * @return array Original sortation array
	 */
	public function getSortations() : array;


	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Sort\Iface[] $sortation List of objects implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setSortations( array $sortation ) : \Aimeos\MW\Criteria\Iface;


	/**
	 * Returns the start number of requested items.
	 *
	 * @return int Start number of the items
	 */
	public function getSliceStart() : int;


	/**
	 * Returns the number of requested items.
	 *
	 * @return int Number of items
	 */
	public function getSliceSize() : int;


	/**
	 * Sets the start number and the size of the requested data slice.
	 *
	 * @param int $start Start number of the items
	 * @param int $size Number of items
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setSlice( int $start, int $size = 100 );


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
	 * @return \Aimeos\MW\Criteria\Expression\Iface|null Condition expressions (maybe nested) or null for none
	 * @throws \Aimeos\MW\Common\Exception If given array is invalid
	 */
	public function toConditions( array $array ) : ?\Aimeos\MW\Criteria\Expression\Iface;


	/**
	 * Creates sortation expressions from an associative array.
	 *
	 * The array must be a single-dimensional array of name and operator pairs like
	 * 	$array = array(
	 * 		'name' => '+',
	 * 		'name2' => '-',
	 * 	);
	 *
	 * @param string[] $array Single-dimensional array of name and operator pairs
	 * @return array List of sort expressions implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 */
	public function toSortations( array $array ) : array;


	/**
	 * Returns the list of translated colums
	 *
	 * @param array $columns List of objects implementing getName() method
	 * @param array $translations Associative list of item names that should be translated
	 * @return array List of translated columns
	 */
	public function translate( array $columns, array $translations = [] ) : array;
}
