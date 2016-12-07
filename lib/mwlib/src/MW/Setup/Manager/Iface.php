<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\Manager;


/**
 * Interface for all setup manager classes
 *
 * @package MW
 * @subpackage Setup
 */
interface Iface
{
	/**
	 * Updates the schema and migrates the data
	 *
	 * @param string|null $task Name of the task
	 * @return null
	 */
	public function migrate( $task = null );

	/**
	 * Undo all schema changes and migrate data back
	 *
	 * @param string|null $task Name of the task
	 * @return null
	 */
	public function rollback( $task = null );

	/**
	 * Cleans up old data required for roll back
	 *
	 * @param string|null $task Name of the task
	 * @return null
	 */
	public function clean( $task = null );

	/**
	 * Executes all tasks for the given database type.
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 * @return null
	 * @deprecated 2016.05
	 */
	public function run( $dbtype );
}
