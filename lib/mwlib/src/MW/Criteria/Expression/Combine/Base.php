<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression\Combine;


/**
 * Base class for combining objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base implements Iface
{
	private $expressions = [];
	private $operator;


	/**
	 * Initializes the object.
	 *
	 * @param string $operator The used combine operator
	 * @param array $list List of expression objects
	 */
	public function __construct( string $operator, array $list )
	{
		$list = array_filter( $list ); // remove NULL values

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MW\Criteria\Expression\Iface::class, $list );

		$this->operator = $operator;
		$this->expressions = $list;
	}


	/**
	 * Returns an array representation of the expression that can be parsed again
	 *
	 * @return array Multi-dimensional expression structure
	 */
	public function __toArray() : array
	{
		$list = [];

		foreach( $this->expressions as $expr ) {
			$list[] = $expr->__toArray();
		}

		return [$this->operator => $list];
	}


	/**
	 * Returns the list of expressions that should be combined.
	 *
	 * @return array List of expressions implementing \Aimeos\MW\Criteria\Expression\Iface
	 */
	public function getExpressions() : array
	{
		return $this->expressions;
	}


	/**
	 * Returns the operator used for the expression.
	 *
	 * @return string Operator used for the expression
	 */
	public function getOperator() : string
	{
		return $this->operator;
	}
}
