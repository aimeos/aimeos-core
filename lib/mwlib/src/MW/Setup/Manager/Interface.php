<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Interface for all setup manager classes
 *
 * @package MW
 * @subpackage Setup
 */
interface MW_Setup_Manager_Interface
{
	/**
	 * Executes all tasks for the given database type.
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 * @return void
	 */
	public function run( $dbtype );
}
