<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates type values in order table.
 */
class OrderMigrateType extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_order" SET "type" = \'repeat\' WHERE "type" = \'0\'',
		'UPDATE "mshop_order" SET "type" = \'web\' WHERE "type"=\'1\'',
		'UPDATE "mshop_order" SET "type" = \'phone\' WHERE "type"=\'2\''
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating order type', 0 ); $this->status( '' );
		$cntRows = 0;
		$table = 'mshop_order';

		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true )
		{
			foreach( $stmts as $sql ) {
				$stmt = $this->conn->create( $sql );
				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}
			if( $cntRows ) {
				$this->status( sprintf( 'migrated (%1$d)', $cntRows ) );
			} else {
				$this->status( 'OK' );
			}
		}
		else
		{
			$this->status( 'OK' );
		}

	}

}
