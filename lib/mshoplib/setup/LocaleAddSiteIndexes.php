<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class LocaleAddSiteIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'idx_mslocsi_nlt_nrt_lvl_pid' => 'CREATE INDEX "idx_mslocsi_nlt_nrt_lvl_pid" ON "mshop_locale_site" ("nleft", "nright", "level", "parentid")',
		'idx_mslocsi_status_level' => 'CREATE INDEX "idx_mslocsi_status_level" ON "mshop_locale_site" ("status", "level")',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Update database schema
	 */
	public function migrate()
	{
		$this->msg( 'Adding indexes to "mshop_locale_site"', 0 ); $this->status( '' );

		$schema = $this->getSchema( 'db-locale' );

		foreach( $this->list as $idx => $stmt )
		{
			$this->msg( sprintf( 'Checking index "%1$s"', $idx ), 0 );

			if( $schema->tableExists( 'mshop_locale_site' ) === true
				&& $schema->indexExists( 'mshop_locale_site', $idx ) === false )
			{
				$this->execute( $stmt );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
