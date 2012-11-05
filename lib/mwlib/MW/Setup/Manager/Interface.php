<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
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
	 * Initializes the setup manager.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param array $dbconfig Associative list with "adapter", "host", "database", "username" and "password" keys
	 * @param array|string $taskpath Filesystem paths to the directory which contains the task classes
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 */
	public function __construct( MW_DB_Connection_Interface $conn, array $dbconfig, $taskpath, $additional = null );

	/**
	 * Executes all tasks for the given database type.
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 */
	public function run( $dbtype );
}
