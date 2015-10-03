<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Setup
 */


/**
 * Common interface all tasks have to implement.
 *
 * @package MW
 * @subpackage Setup
 */
interface MW_Setup_Task_Iface
{
	/**
	 * Initializes the task object.
	 *
	 * @param MW_Setup_DBSchema_Iface $schema Database schema object
	 * @param MW_DB_Connection_Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @return void
	 */
	public function __construct( MW_Setup_DBSchema_Iface $schema, MW_DB_Connection_Iface $conn, $additional = null );

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies();

	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies();

	/**
	 * Executes the task for the given database type.
	 *
	 * @param string $dbtype Database type (mysql)
	 * @return void
	 */
	public function run( $dbtype );
}
