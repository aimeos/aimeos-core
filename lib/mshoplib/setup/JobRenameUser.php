<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames "user" column to "editor" in job table.
 */
class JobRenameUser extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'user' => 'ALTER TABLE "madmin_job" CHANGE "user" "editor" VARCHAR(255) NOT NULL',
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return [];
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$table = "madmin_job";
		$this->msg( sprintf( 'Renaming in "%1$s" column "user" to "editor".', $table ), 0 );
		$this->status( '' );

		foreach( $stmts as $column => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, $column ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}

}
