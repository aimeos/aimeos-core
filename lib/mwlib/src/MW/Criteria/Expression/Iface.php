<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Expression;


/**
 * Interface for expression objects (compare and combine).
 *
 * @package MW
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Generates a string from the expression objects.
	 *
	 * @param array $types Associative list of variable or column names as keys and their corresponding types
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @param array $plugins Associative list of item names and plugins implementing \Aimeos\MW\Criteria\Plugin\Iface
	 * @return string Expression that evaluates to a boolean result
	 */
	public function toString( array $types, array $translations = [], array $plugins = [] );


	/**
	 * Returns the operator for combining or comparing the expressions.
	 *
	 * @return string Operator for combining or comparing
	 */
	public function getOperator();


	/**
	 * Returns the available operators for the expression.
	 *
	 * @return array List of available operators
	 */
	public static function getOperators();


	/**
	 * Translates the sort key into the name required by the storage
	 *
	 * @param array $translations Associative list of variable or column names that should be translated
	 * @return string|null Translated name (with replaced parameters if the name is an expression function)
	 */
	public function translate( array $translations );
}
