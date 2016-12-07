<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Fixes the old email flag values migrated from the order table.
 */
class OrderFixEmailStatus extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'update' => '
			UPDATE "mshop_order_status"
			SET "siteid"=(SELECT "siteid" FROM "mshop_order" mord WHERE mord."id"="parentid" LIMIT 1 ),
				"ctime"=NOW(), "mtime"=NOW(), "editor"=\'setup task\'
			WHERE "siteid" IS NULL
		',
		'change' => '
			UPDATE "mshop_order_status"
			SET "type"=\'email-delivery\', "value"=?
			WHERE "type"=?
		',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderMigrateEmailflag' );
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
	 * Migrates the emailflag status values in order table to the order status table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Fixing order email status values', 0 );

		if( $this->schema->tableExists( 'mshop_order_status' ) === true )
		{
			$this->execute( $stmts['update'] );

			$mapping = array(
				0 => 'email-deleted',
				1 => 'email-pending',
				2 => 'email-progress',
				3 => 'email-dispatched',
				4 => 'email-delivered',
				5 => 'email-lost',
				6 => 'email-refused',
				7 => 'email-returned',
			);

			$cntRows = 0;
			foreach( $mapping as $value => $type )
			{
				$stmt = $this->conn->create( $stmts['change'] );
				$stmt->bind( 1, $value, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, $type );

				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}

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
