<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array;

	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array;

	/**
	 * Updates the schema and migrates the data
	 *
	 * @return void
	 */
	public function migrate();

	/**
	 * Sets the database manager object
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database manager
	 * @return Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $dbm ) : Iface;

	/**
	 * Sets the associative list of schemas with the resource name as key.
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface[] $schemas Associative list of schemas
	 * @return Aimeos\MW\Setup\Task\Iface Task object for fluent interface
	 */
	public function setSchemas( array $schemas ) : Iface;
}
