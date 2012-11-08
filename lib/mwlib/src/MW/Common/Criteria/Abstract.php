<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id: Abstract.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Abstract search class
 *
 * @package MW
 * @subpackage Common
 */
abstract class MW_Common_Criteria_Abstract implements MW_Common_Criteria_Interface
{
	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 */
	public function createFunction( $name, array $params )
	{
		return MW_Common_Criteria_Expression_Abstract::createFunction( $name, $params );
	}


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
	public function toConditions( array $array )
	{
		if( count( $array ) === 0 ) {
			return $this->compare( '==', '1', '1' );
		}

		if( ( list( $op, $value ) = each( $array ) ) === false ) {
			throw new MW_Common_Exception( sprintf( 'Invalid condition array "%1$s"', json_encode( $array ) ) );
		}

		$operators = $this->getOperators();

		if( in_array( $op, $operators['combine'] ) ) {
			return $this->_createCombineExpression( $op, (array) $value );
		}
		else if( in_array( $op, $operators['compare'] ) ) {
			return $this->_createCompareExpression( $op, (array) $value );
		}

		throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
	}


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
	public function toSortations( array $array )
	{
		$results = array();

		foreach( $array as $name => $op ) {
			$results[] = $this->sort( $op, $name );
		}

		return $results;
	}


	/**
	 * Creates a "combine" expression.
	 *
	 * @param string $operator One of the "combine" operators
	 * @param array $list List of arrays with "combine" or "compare" representations
	 * @throws MW_Common_Exception If operator is invalid
	 */
	protected function _createCombineExpression( $operator, array $list )
	{
		$results = array();
		$operators = $this->getOperators();

		foreach( $list as $entry )
		{
			$entry = (array) $entry;

			if( ( list( $op, $value ) = each( $entry ) ) === false ) {
				throw new MW_Common_Exception( sprintf( 'Invalid combine condition array "%1$s"', json_encode( $entry ) ) );
			}

			if( in_array( $op, $operators['combine'] ) ) {
				$results[] = $this->_createCombineExpression( $op, (array) $entry[$op] );
			}
			else if( in_array( $op, $operators['compare'] ) ) {
				$results[] = $this->_createCompareExpression( $op, (array) $entry[$op] );
			}
			else {
				throw new MW_Common_Exception( sprintf( 'Invalid operator "%1$s"', $op ) );
			}
		}

		return $this->combine( $operator, $results );
	}


	/**
	 * Creates a "compare" expression.
	 *
	 * @param string $operator One of the "compare" operators
	 * @param array $list Associative list containing one name/value pair
	 * @throws MW_Common_Exception If no name/value pair is available
	 */
	protected function _createCompareExpression( $op, array $pair )
	{
		if( ( list( $name, $value ) = each( $pair ) ) === false ) {
			throw new MW_Common_Exception( sprintf( 'Invalid compare condition array "%1$s"', json_encode( $pair ) ) );
		}

		return $this->compare( $op, $name, $value );
	}
}
