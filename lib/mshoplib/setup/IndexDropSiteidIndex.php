<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrate the database schema
 */
class IndexDropSiteidIndex extends \Aimeos\MW\Setup\Task\Base
{
	private $list = array(
		'mysql' => 'DROP INDEX "idx_msindte_sid" ON "mshop_index_text"',
		'pgsql' => 'DROP INDEX "idx_msindte_sid"',
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
		$this->msg( 'Dropping index "idx_msindte_sid"', 0 );

		$schema = $this->getSchema( 'db-index' );

		if( isset( $this->list[$schema->getName()] )
			&& $schema->tableExists( 'mshop_index_text' ) === true
			&& $schema->indexExists( 'mshop_index_text', 'idx_msindte_sid' ) === true )
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
