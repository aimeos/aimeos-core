<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required tables.
 */
class TablesCreateMAdmin extends TablesCreateMShop
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
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
			'db-cache' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'cache.php',
			'db-log' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'log.php',
			'db-job' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'job.php',
			'db-queue' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'queue.php',
		);

		$this->setupSchema( $files );
	}
}
