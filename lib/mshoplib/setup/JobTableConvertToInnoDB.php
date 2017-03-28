<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Converts job table to InnoDB.
 */
class JobTableConvertToInnoDB extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'convert' => 'ALTER TABLE "madmin_job" ENGINE=InnoDB',
		'check' => 'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?'
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
	 * Converts MyISAM table to InnoDB.
	 *
	 * @param array $stmts List of SQL statements to execute for changing table
	 */
	protected function process( array $stmts )
	{
		$table = "madmin_job";
		$this->msg( sprintf( 'Converting "%1$s" to InnoDB".', $table ), 0 );

		$stmt = $this->conn->create( $stmts['check'] );
		$stmt->bind( 1, $this->schema->getDBName() );
		$stmt->bind( 2, $table );
		$result = $stmt->execute();
		$dbname = $result->fetch();
		$result->finish();

		if( $this->schema->tableExists( $table ) === true
			&& $dbname['ENGINE'] === 'MyISAM' )
		{
			$this->execute( $stmts['convert'] );
			$this->status( 'converted' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}
