<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 */


/**
 * Required methods for database manager objects.
 *
 * @package MW
 * @subpackage DB
 */
interface MW_DB_Manager_Iface
{
	/**
	 * Returns a database connection.
	 *
	 * @param string $name Name of the resource in configuration
	 * @return MW_DB_Connection_Iface
	 */
	public function acquire( $name = 'db' );


	/**
	 * Releases the connection for reuse
	 *
	 * @param MW_DB_Connection_Iface $connection Connection object
	 * @param string $name Name of resource
	 * @return void
	 */
	public function release( MW_DB_Connection_Iface $connection, $name = 'db' );
}
