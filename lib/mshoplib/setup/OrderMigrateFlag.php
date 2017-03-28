<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the flag values in order table to the order status table.
 */
class OrderMigrateFlag extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'migrate' => '
			INSERT INTO "mshop_order_status" ("parentid", "type", "value")
			SELECT "id", ?, 1 FROM "mshop_order" AS mord
			WHERE "flag" > 0 && "flag" & ? > 0 AND NOT EXISTS (
				SELECT 1 FROM "mshop_order_status" AS mordst
				WHERE mord."id"=mordst."parentid" AND mordst."type"=? AND mordst."value"=\'1\'
			)
		',
		'index' => 'DROP INDEX "idx_msord_sid_pstat_dstat_flag" ON "mshop_order"',
		'column' => 'ALTER TABLE "mshop_order" DROP COLUMN "flag"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderAddFlags' );
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
	 * Migrates the *flag status values in order table to the order status table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Migrating order flags to order status list', 0 ); $this->status( '' );
		$table = 'mshop_order';

		if( $this->schema->tableExists( $table ) === true )
		{
			$this->msg( 'Deleting order index with flag colum', 1 );

			if( $this->schema->indexExists( $table, 'idx_msord_sid_pstat_dstat_flag' ) === true )
			{
				$this->execute( $stmts['index'] );
				$this->status( 'dropped' );
			}
			else
			{
				$this->status( 'OK' );
			}


			$this->msg( 'Migrating order flag colum', 1 );

			if( $this->schema->columnExists( $table, 'flag' ) === true )
			{
				$cntRows = 0;
				$mapping = [];

				for( $i = 1; $i < 16; $i++ ) {
					$mapping[0x1 << $i] = 0x1 << $i;
				}

				$mapping[1] = 'stock-update';

				foreach( $mapping as $key => $value )
				{
					$stmt = $this->conn->create( $stmts['migrate'] );
					$stmt->bind( 1, $value );
					$stmt->bind( 2, $key );
					$stmt->bind( 3, $value );

					$result = $stmt->execute();
					$cntRows += $result->affectedRows();
					$result->finish();
				}

				$this->execute( $stmts['column'] );

				if( $cntRows > 0 ) {
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

}
