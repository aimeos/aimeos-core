<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Setup
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Common interface all tasks have to implement.
 *
 * @package MW
 * @subpackage Setup
 */
interface Iface
{
	/**
	 * Initializes the task object.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema Database schema object
	 * @param \Aimeos\MW\DB\Connection\Iface $conn Database connection
	 * @param mixed $additional Additionally provided information for the setup tasks if required
	 * @return null
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null );

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
	 * Updates the schema and migrates the data
	 *
	 * @return void
	 */
	public function migrate();

	/**
	 * Undo all schema changes and migrate data back
	 *
	 * @return void
	 */
	public function rollback();

	/**
	 * Cleans up old data required for roll back
	 *
	 * @return void
	 */
	public function clean();
}
