<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * Returns an array representation of the expression that can be parsed again
	 *
	 * @return array Multi-dimensional expression structure
	 */
	public function __toArray() : array;

	/**
	 * Adds a new expression to the existing list combined by the AND operator.
	 *
	 * You can add expression is three ways:
	 *
	 * - Name, operator and value:
	 *   $f->add( 'product.code', '==', 'abc' );
	 *
	 * - Name/value pairs and optional operator ("==" by default):
	 *   $f->add( ['product.type' => 'voucher', 'product.status' => 1], '!=' );
	 *   $f->add( ['product.type' => 'default', 'product.status' => 1] );
	 *
	 * - Single expression:
	 *   $f->add( $f->is( 'product.code', '==', 'abc' ) );
	 *   $f->add( $f->and( [$f->is( 'product.code', '==', 'abc' ), $f->is( 'product.status', '>', 0 )] );
	 *   $f->add( $f->or( [$f->is( 'product.code', '==', 'abc' ), $f->is( 'product.label', '=~', 'abc' )] );
	 *   $f->add( $f->not( $f->is( 'product.code', '=~', 'abc' ) );
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Combine\Iface|\Aimeos\MW\Criteria\Expression\Compare\Iface|array|string Expression, list of name/value pairs or name
	 * @param string $operator Operator to compare name and value with
	 * @param mixed $value Value to compare the name with
	 */
	public function add( $expr, string $operator = '==', $value = null ) : \Aimeos\MW\Criteria\Iface;

	/**
	 * Combines the expression with an AND operator
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Compare\Iface[] $list List of expression objects
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function and( array $list ) : \Aimeos\MW\Criteria\Expression\Combine\Iface;

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
	 * "=~": item STARTS WITH value
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
	 * Returns the number of requested items.
	 *
	 * @return int Number of items
	 */
	public function getLimit() : int;

	/**
	 * Returns the start number of requested items.
	 *
	 * @return int Start number of the items
	 */
	public function getOffset() : int;

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
	 * Creates a new compare expression.
	 *
	 * Available comparision operators are:
	 * "==": item EQUAL value
	 * "!=": item NOT EQUAL value
	 * "=~": item STARTS WITH value
	 * "~=": item CONTAINS value
	 * ">=": item GREATER OR EQUAL value
	 * "<=": item SMALLER OR EQUAL value
	 * ">": item GREATER value
	 * "<": item SMALLER value
	 *
	 * @param string $name Name of the column or property that should be used for comparison
	 * @param string $operator One of the known operators
	 * @param mixed $value Value the column or property should be compared to
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
	 */
	public function is( string $name, string $operator, $value ) : \Aimeos\MW\Criteria\Expression\Compare\Iface;

	/**
	 * Creates a function signature for expressions used in is() and add().
	 *
	 * @param string $name Function name without parentheses
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 * @return string Function signature
	 */
	public function make( string $name, array $params ) : string;

	/**
	 * Negates the whole expression.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $expr Expression object
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function not( \Aimeos\MW\Criteria\Expression\Iface $expr ) : \Aimeos\MW\Criteria\Expression\Combine\Iface;

	/**
	 * Combines the expression with an OR operator
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Compare\Iface[] $list List of expression objects
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function or( array $list ) : \Aimeos\MW\Criteria\Expression\Combine\Iface;

	/**
	 * Sets the keys the data should be ordered by.
	 *
	 *
	 * Available sorting operators are:
	 * "product.label": sort ascending
	 * "-product.label": sort descending
	 *
	 * @param array|string $keys Name of the column or property that should be used for sorting
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function order( $keys ) : \Aimeos\MW\Criteria\Iface;

	/**
	 * Creates condition expressions from a multi-dimensional associative array.
	 *
	 * The simplest form of a valid associative array is a single comparison:
	 * 	$array = [
	 * 		'==' => ['name' => 'value'],
	 * 	];
	 *
	 * Combining several conditions can look like:
	 * 	$array = [
	 * 		'&&' => [
	 * 			['==' => ['name' => 'value']],
	 * 			['==' => ['name2' => 'value2']],
	 * 		],
	 * 	];
	 *
	 * Nested combine operators are also possible.
	 *
	 * @param array $array Multi-dimensional associative array containing the expression arrays
	 * @return \Aimeos\MW\Criteria\Expression\Iface|null Condition expressions (maybe nested) or null for none
	 * @throws \Aimeos\MW\Exception If given array is invalid
	 */
	public function parse( array $array ) : ?\Aimeos\MW\Criteria\Expression\Iface;

	/**
	 * Sets the expression objects.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $conditions Expression object
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setConditions( \Aimeos\MW\Criteria\Expression\Iface $conditions ) : Iface;

	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Sort\SQL[] $sortations List of objects implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setSortations( array $sortations ) : Iface;

	/**
	 * Sets the offset and the size of the requested data slice.
	 *
	 * @param int $start Start number of the items
	 * @param int $size Number of items
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function slice( int $offset, int $limit = 100 ) : \Aimeos\MW\Criteria\Iface;

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
	 * Returns the list of translated colums
	 *
	 * @param array $columns List of objects implementing getName() method
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return array List of translated columns
	 */
	public function translate( array $columns, array $translations = [], array $funcs = [] ) : array;
}
