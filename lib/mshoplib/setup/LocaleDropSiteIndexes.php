<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class LocaleDropSiteIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'idx_mslocsi_nleft_nright' => array(
			'mysql' => 'DROP INDEX "idx_mslocsi_nleft_nright" ON "mshop_locale_site"',
			'pgsql' => 'DROP INDEX "idx_mslocsi_nleft_nright"',
		),
		'idx_mslocsi_level_status' => array(
			'mysql' => 'DROP INDEX "idx_mslocsi_level_status" ON "mshop_locale_site"',
			'pgsql' => 'DROP INDEX "idx_mslocsi_level_status"',
		),
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
		$this->clean();
	}


	/**
	 * Clean up database schema
	 */
	public function clean()
	{
		$this->msg( 'Dropping indexes from "mshop_locale_site"', 0 ); $this->status( '' );

		$schema = $this->getSchema( 'db-locale' );

		foreach( $this->list as $idx => $stmts )
		{
			$this->msg( sprintf( 'Checking index "%1$s"', $idx ), 0 );

			if( isset( $this->list[$schema->getName()] )
				&& $schema->tableExists( 'mshop_locale_site' ) === true
				&& $schema->indexExists( 'mshop_locale_site', $idx ) === true )
			{
				$this->execute( $this->list[$schema->getName()] );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
