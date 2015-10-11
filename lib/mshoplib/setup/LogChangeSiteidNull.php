<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes madmin log table to allow site id to be null.
 */
class LogChangeSiteidNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = 'ALTER TABLE "madmin_log" CHANGE COLUMN "siteid" "siteid" INTEGER';

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
	}

	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMAdmin' );
	}

	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}

	/**
	 * Changes site ID to NULL in madmin_log.
	 *
	 * @param string $stmt SQL statement to execute
	 */
	protected function process( $stmt )
	{
		$table = 'madmin_log';

		$this->msg( 'Changing site ID to NULL in madmin_log', 0 );
		$this->status( '' );

		$this->msg( sprintf( 'Changing table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) &&
			!$this->schema->getColumnDetails( $table, 'siteid' )->isNullable() )
		{
			$this->execute( $stmt );
			$this->status( 'done' );
		} else {
			$this->status( 'OK' );
		}

	}

}
