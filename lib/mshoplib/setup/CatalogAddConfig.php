<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds config column to catalog table.
 */
class CatalogAddConfig extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "mshop_catalog" ADD "config" TEXT NOT NULL AFTER "label"';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogTreeToCatalog' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param string $stmt SQL statement to execute for adding columns
	 */
	protected function process( $stmt )
	{
		$this->msg( 'Adding config column to mshop_catalog', 0 );

		if( $this->schema->tableExists( 'mshop_catalog' ) === true
			&& $this->schema->columnExists( 'mshop_catalog', 'config' ) === false )
		{
			$this->execute( $stmt );
			$this->status( 'added' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}