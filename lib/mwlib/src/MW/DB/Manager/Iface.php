<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB\Manager;


/**
 * Required methods for database manager objects.
 *
 * @package MW
 * @subpackage DB
 */
interface Iface
{
	/**
	 * Returns a database connection.
	 *
	 * @param string $name Name of the resource in configuration
	 * @return \Aimeos\MW\DB\Connection\Iface
	 */
	public function acquire( $name = 'db' );


	/**
	 * Releases the connection for reuse
	 *
	 * @param \Aimeos\MW\DB\Connection\Iface $connection Connection object
	 * @param string $name Name of resource
	 * @return void
	 */
	public function release( \Aimeos\MW\DB\Connection\Iface $connection, $name = 'db' );
}
