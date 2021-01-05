<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required tables.
 */
class TablesCreateMAdmin extends TablesCreateMShop
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Removes old columns and sequences
	 */
	public function clean()
	{
		$this->msg( 'Cleaning admin tables', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-cache' => 'default' . $ds . 'schema' . $ds . 'cache.php',
			'db-log' => 'default' . $ds . 'schema' . $ds . 'log.php',
			'db-job' => 'default' . $ds . 'schema' . $ds . 'job.php',
			'db-queue' => 'default' . $ds . 'schema' . $ds . 'queue.php',
		);

		$this->setupSchema( $files, true );
	}


	/**
	 * Creates the MAdmin tables
	 */
	public function migrate()
	{
		$this->msg( 'Creating admin tables', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-cache' => 'default' . $ds . 'schema' . $ds . 'cache.php',
			'db-log' => 'default' . $ds . 'schema' . $ds . 'log.php',
			'db-job' => 'default' . $ds . 'schema' . $ds . 'job.php',
			'db-queue' => 'default' . $ds . 'schema' . $ds . 'queue.php',
		);

		$this->setupSchema( $files );
	}
}
