<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Creates PHP expressions
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_PHP extends MW_Common_Criteria_Abstract
{
	private $conditions;
	private $sortations = array();
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
	 * @return MW_Common_Criteria_Expression_Combine_Interface Combine expression object
	 */
	public function combine( $operator, array $list )
	{
		return new MW_Common_Criteria_Expression_Combine_PHP( $operator, $list );
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
		return new MW_Common_Criteria_Expression_Compare_PHP( $operator, $name, $value );
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
		return new MW_Common_Criteria_Expression_Sort_PHP( $operator, $name );
	}


	/**
	 * Returns the available compare, combine and sort operators.
	 *
	 * @return array Associative list of lists (compare, combine, sort) containing the available operators
	 */
	public function getOperators()
	{
		return array(
			'combine' => MW_Common_Criteria_Expression_Combine_PHP::getOperators(),
			'compare' => MW_Common_Criteria_Expression_Compare_PHP::getOperators(),
			'sort' => MW_Common_Criteria_Expression_Sort_PHP::getOperators(),
		);
	}


	/**
	 * Returns the expression string used for generating the expression.
	 *
	 * @param array $types Associative list of item names and their types
	 * @param array $translations Associative list of item names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing MW_Common_Criteria_Plugin_Interface
	 * @return string Expression string for searching
	 */
	public function getConditionString( array $types, array $translations = array(), array $plugins = array() )
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
	 * @return MW_Common_Criteria_Expression_Interface Original expression objects
	 */
	public function getConditions()
	{
		return $this->conditions;
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
	public function getSortationString( array $types, array $translations = array() )
	{
		if( empty( $this->sortations ) )
		{
			reset( $types );

			if( ( $name = key( $types ) ) === false ) {
				throw new MW_Common_Exception( 'No sortation types available' );
			}

			return $this->sort( '+', $name )->toString( $types, $translations );
		}


		$sortation = array();

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
	 * @param MW_Common_Criteria_Expression_Sort_PHP[] $sortations List of objects implementing MW_Common_Criteria_Expression_Sort_Interface
	 * @return MW_Common_Criteria_Interface Object instance for fluent interface
	 */
	public function setSortations( array $sortations )
	{
		MW_Common_Abstract::checkClassList( 'MW_Common_Criteria_Expression_Sort_Interface', $sortations );

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
	* @return MW_Common_Criteria_PHP Object instance for fluent interface
	*/
	public function setSlice( $start, $size = 100 )
	{
		$this->sliceStart = (int) $start;
		$this->sliceSize = (int) $size;

		return $this;
	}
}
