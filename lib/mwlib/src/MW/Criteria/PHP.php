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
 * Creates PHP expressions
 *
 * @package MW
 * @subpackage Common
 */
class PHP extends \Aimeos\MW\Criteria\Base
{
	private $conditions;
	private $sortations = [];
	private $sliceStart = 0;
	private $sliceSize = 100;


	/**
	 * Initializes the PHP search object
	 */
	public function __construct()
	{
		$this->conditions = $this->compare( '==', '1', '1' );
	}


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
	public function combine( $operator, array $list )
	{
		return new \Aimeos\MW\Criteria\Expression\Combine\PHP( $operator, $list );
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
	 * @return \Aimeos\MW\Criteria\Expression\Compare\Iface Compare expression object
	 */
	public function compare( $operator, $name, $value )
	{
		return new \Aimeos\MW\Criteria\Expression\Compare\PHP( $operator, $name, $value );
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
	 * @return \Aimeos\MW\Criteria\Expression\Sort\Iface Sort expression object
	 */
	public function sort( $operator, $name )
	{
		return new \Aimeos\MW\Criteria\Expression\Sort\PHP( $operator, $name );
	}


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators()
	{
		return array(
			'combine' => \Aimeos\MW\Criteria\Expression\Combine\PHP::getOperators(),
			'compare' => \Aimeos\MW\Criteria\Expression\Compare\PHP::getOperators(),
			'sort' => \Aimeos\MW\Criteria\Expression\Sort\PHP::getOperators(),
		);
	}


	/**
	 * Returns the expression string used for generating the expression.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return string Expression string for searching
	 */
	public function getConditionString( array $types, array $translations = [], array $plugins = [] )
	{
		$types['1'] = 'int';

		if( ( $string = $this->conditions->toString( $types, $translations, $plugins ) ) !== '' ) {
			return $string;
		}

		return '1==1';
	}


	/**
	 * Returns the original condition expression objects.
	 *
	 * @return \Aimeos\MW\Criteria\Expression\Iface Original expression objects
	 */
	public function getConditions()
	{
		return $this->conditions;
	}


	/**
	 * Sets the expression objects.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface $conditions Expression object
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setConditions( \Aimeos\MW\Criteria\Expression\Iface $conditions )
	{
		if( $conditions instanceof \Aimeos\MW\Criteria\Expression\Sort\Iface ) {
			throw new \Aimeos\MW\Common\Exception( 'Sortation objects are not allowed' );
		}

		$this->conditions = $conditions;

		return $this;
	}


	/**
	 * Returns the string for sorting the result.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @return string Order string for sorting the items
	 */
	public function getSortationString( array $types, array $translations = [] )
	{
		if( empty( $this->sortations ) )
		{
			reset( $types );

			if( ( $name = key( $types ) ) === false ) {
				throw new \Aimeos\MW\Common\Exception( 'No sortation types available' );
			}

			return $this->sort( '+', $name )->toString( $types, $translations );
		}


		$sortation = [];

		foreach( $this->sortations as $sortitem )
		{
			if( ( $string = $sortitem->toString( $types, $translations ) ) !== '' ) {
				$sortation[] = $string;
			}
		}

		return implode( ' ', $sortation );
	}


	/**
	 * Returns the original sorting array for ordering the results.
	 *
	 * @return array Original sortation array
	 */
	public function getSortations()
	{
		return $this->sortations;
	}


	/**
	 * Stores the sortation objects for sorting the result.
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Sort\PHP[] $sortations List of objects implementing \Aimeos\MW\Criteria\Expression\Sort\Iface
	 * @return \Aimeos\MW\Criteria\Iface Object instance for fluent interface
	 */
	public function setSortations( array $sortations )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MW\\Criteria\\Expression\\Sort\\Iface', $sortations );

		$this->sortations = $sortations;

		return $this;
	}


	/**
	 * Returns the number of requested items.
	 *
	 * @return integer Number of items
	 */
	public function getSliceSize()
	{
		return $this->sliceSize;
	}


	/**
	 * Returns the start number of requested items.
	 *
	 * @return integer Start number of the items
	 */
	public function getSliceStart()
	{
		return $this->sliceStart;
	}


	/**
	 * Sets the start number and the size of the requested data slice.
	 *
	 * @param integer $start Start number of the items
	 * @param integer $size Number of items
	 * @return \Aimeos\MW\Criteria\PHP Object instance for fluent interface
	 */
	public function setSlice( $start, $size = 100 )
	{
		$this->sliceStart = (int) $start;
		$this->sliceSize = (int) $size;

		return $this;
	}
}
