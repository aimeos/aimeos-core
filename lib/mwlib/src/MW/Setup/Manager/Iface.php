<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	 * Executes all tasks for the given database type.
	 *
	 * @param string $dbtype Name of the database type (mysql, etc.)
	 * @return void
	 */
	public function run( $dbtype );
}
