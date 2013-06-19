<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Required methods for all database statement objects.
 *
 * @package MW
 * @subpackage DB
 */
interface MW_DB_Statement_Interface
{
	/**
	 * Binds a value to a parameter in the statement.
	 *
	 * @param integer $position Position index of the placeholder
	 * @param mixed $value Value which should be bound to the placeholder
	 * @param integer $type Type of given value defined in MW_DB_Stmt_Abstract as constant
	 */
	public function bind( $position, $value, $type = MW_DB_Statement_Abstract::PARAM_STR);

	/**
	 * Executes the statement.
	 *
	 * @return MW_DB_Result_Interface Result object
	 */
	public function execute();
}
