<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\Base\DB\Manager;


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
	 * @return \Aimeos\Base\DB\Connection\Iface
	 */
	public function acquire( string $name = 'db' );


	/**
	 * Releases the connection for reuse
	 *
	 * @param \Aimeos\Base\DB\Connection\Iface $connection Connection object
	 * @param string $name Name of resource
	 * @return void
	 */
	public function release( \Aimeos\Base\DB\Connection\Iface $connection, string $name = 'db' );
}
