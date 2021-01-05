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
 * Abstract search class
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base implements \Aimeos\MW\Criteria\Iface
{
	/**
	 * Returns an array representation of the expression that can be parsed again
	 *
	 * @return array Multi-dimensional expression structure
	 */
	public function __toArray() : array
	{
		$cond = $this->getConditions();
		return $cond ? $cond->__toArray() : [];
	}


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
	 * @param \Aimeos\MW\Criteria\Expression\Combine\Iface|\Aimeos\MW\Criteria\Expression\Compare\Iface|array|string|null Expression, list of name/value pairs or name
	 * @param string $operator Operator to compare name and value with
	 * @param mixed $value Value to compare the name with
	 * @return \Aimeos\MW\Criteria\Iface Same object for fluent interface
	 */
	public function add( $expr, string $operator = '==', $value = null ) : \Aimeos\MW\Criteria\Iface
	{
		$cond = [];

		if( is_null( $expr ) ) {
			return $this;
		}

		if( is_string( $expr ) ) {
			$cond[] = $this->compare( $operator, $expr, $value );
		}

		if( is_array( $expr ) )
		{
			$list = [];

			foreach( $expr as $name => $value ) {
				$list[] = $this->compare( $operator, $name, $value );
			}

			$cond[] = $this->and( $list );
		}

		if( $expr instanceof \Aimeos\MW\Criteria\Expression\Combine\Iface
			|| $expr instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface
		) {
			$cond[] = $expr;
		}

		if( !empty( $cond ) )
		{
			$cond[] = $this->getConditions();
			return $this->setConditions( $this->and( $cond ) );
		}

		$msg = 'Use a column name, an array of name/value pairs or the result from and(), or(), not() or is() as first argument for add()';
		throw new \Aimeos\MW\Exception( $msg );
	}


	/**
	 * Combines the expression with an AND operator
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Compare\Iface[] $list List of expression objects
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function and( array $list ) : \Aimeos\MW\Criteria\Expression\Combine\Iface
	{
		return $this->combine( '&&', $list );
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
	 * @param string $name Name of the column or property that should be used for comparison
	 * @param string $operator One of the known operators
	 * @param mixed $value Value the column or property should be compared to
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
	 */
	public function is( string $name, string $operator, $value ) : \Aimeos\MW\Criteria\Expression\Compare\Iface
	{
		return $this->compare( $operator, $name, $value );
	}


	/**
	 * Creates a function signature for expressions used in is() and add().
	 *
	 * @param string $name Function name without parentheses
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 * @return string Function signature
	 */
	public function make( string $name, array $params ) : string
	{
		return $name . '(' . substr( json_encode( $params ), 1, -1 ) . ')';
	}


	/**
	 * Negates the whole expression.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $expr Expression object
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function not( \Aimeos\MW\Criteria\Expression\Iface $expr ) : \Aimeos\MW\Criteria\Expression\Combine\Iface
	{
		return $this->combine( '!', [$expr] );
	}


	/**
	 * Combines the expression with an OR operator
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Compare\Iface[] $list List of expression objects
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 */
	public function or( array $list ) : \Aimeos\MW\Criteria\Expression\Combine\Iface
	{
		return $this->combine( '||', $list );
	}


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
	public function order( $names ) : \Aimeos\MW\Criteria\Iface
	{
		$sort = [];

		foreach( (array) $names as $name )
		{
			$op = '+';
			$name = (string) $name;

			if( strlen( $name ) && $name[0] === '-' ) {
				$op = '-'; $name = substr( $name, 1 );
			}

			$sort[] = $this->sort( $op, $name );
		}

		return $this->setSortations( $sort );
	}


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
	public function parse( array $array ) : ?\Aimeos\MW\Criteria\Expression\Iface
	{
		if( ( $value = reset( $array ) ) === false ) {
			return null;
		}

		$op = key( $array );
		$operators = $this->getOperators();

		if( in_array( $op, $operators['combine'], true ) ) {
			return $this->createCombineExpression( $op, (array) $value );
		}
		else if( in_array( $op, $operators['compare'], true ) ) {
			return $this->createCompareExpression( $op, (array) $value );
		}

		throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
	}


	/**
	 * Returns the list of translated colums
	 *
	 * @param array $columns List of objects implementing getName() method
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $funcs Associative list of item names and functions modifying the conditions
	 * @return array List of translated columns
	 */
	public function translate( array $columns, array $translations = [], array $funcs = [] ) : array
	{
		$list = [];

		foreach( $columns as $item )
		{
			if( ( $value = $item->translate( $translations, $funcs ) ) !== null ) {
				$list[] = $value;
			}
		}

		return $list;
	}


	/**
	 * Creates a "combine" expression.
	 *
	 * @param string $operator One of the "combine" operators
	 * @param array $list List of arrays with "combine" or "compare" representations
	 * @return \Aimeos\MW\Criteria\Expression\Combine\Iface Combine expression object
	 * @throws \Aimeos\MW\Common\Exception If operator is invalid
	 */
	protected function createCombineExpression( string $operator, array $list )
	{
		$results = [];
		$operators = $this->getOperators();

		foreach( $list as $entry )
		{
			$entry = (array) $entry;

			if( ( $op = key( $entry ) ) === null ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid combine condition array "%1$s"', json_encode( $entry ) ) );
			}

			if( in_array( $op, $operators['combine'], true ) ) {
				$results[] = $this->createCombineExpression( $op, (array) $entry[$op] );
			}
			else if( in_array( $op, $operators['compare'], true ) ) {
				$results[] = $this->createCompareExpression( $op, (array) $entry[$op] );
			}
			else {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
			}
		}

		return $this->combine( $operator, $results );
	}


	/**
	 * Creates a "compare" expression.
	 *
	 * @param string $op One of the "compare" operators
	 * @param array $pair Associative list containing one name/value pair
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
	 * @throws \Aimeos\MW\Common\Exception If no name/value pair is available
	 */
	protected function createCompareExpression( $op, array $pair )
	{
		if( ( $value = reset( $pair ) ) === false ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Invalid compare condition array "%1$s"', json_encode( $pair ) ) );
		}

		return $this->compare( $op, key( $pair ), $value );
	}
}
