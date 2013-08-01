<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Migrates the emailflag values in order table to the order status table.
 */
class MW_Setup_Task_OrderMigrateEmailflag extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'migrate' => '
			INSERT INTO "mshop_order_status" ("parentid", "type", "value")
			SELECT "id", ?, 1 FROM "mshop_order" AS mord
			WHERE "emailflag" > 0 && "emailflag" & ? > 0 AND NOT EXISTS (
				SELECT 1 FROM "mshop_order_status" AS mordst
				WHERE mord."id"=mordst."parentid" AND mordst."type"=? AND mordst."value"=\'1\'
			)
		',
		'index' => 'DROP INDEX "idx_msord_sid_pstat_dstat_email" ON "mshop_order"',
		'column' => 'ALTER TABLE "mshop_order" DROP COLUMN "emailflag"',
	);


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
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Migrates the *flag status values in order table to the order status table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Migrating order emailflag colum to order status list', 0 ); $this->_status( '' );
		$table = 'mshop_order';

		if( $this->_schema->tableExists( $table ) === true )
		{
			$this->_msg( 'Deleting order index with emailflag colum', 1 );

			if( $this->_schema->indexExists( $table, 'idx_msord_sid_pstat_dstat_email' ) === true )
			{
				$this->_execute( $stmts['index'] );
				$this->_status( 'dropped' );
			}
			else
			{
				$this->_status( 'OK' );
			}


			$this->_msg( 'Migrating order emailflag colum', 1 );

			if( $this->_schema->columnExists( $table, 'emailflag' ) === true )
			{
				$cntRows = 0;
				$mapping = array();

				for( $i = 1; $i < 16; $i++ ) {
					$mapping[ 0x1 << $i ] = 0x1 << $i;
				}

				$mapping[1] = 'email-accepted';
				$mapping[2] = 'email-deleted';
				$mapping[4] = 'email-pending';
				$mapping[8] = 'email-progress';
				$mapping[16] = 'email-dispatched';
				$mapping[32] = 'email-delivered';
				$mapping[64] = 'email-lost';
				$mapping[128] = 'email-refused';
				$mapping[256] = 'email-returned';

				foreach( $mapping as $key => $value )
				{
					$stmt = $this->_conn->create( $stmts['migrate'] );
					$stmt->bind( 1, $value );
					$stmt->bind( 2, $key );
					$stmt->bind( 3, $value );

					$result = $stmt->execute();
					$cntRows += $result->affectedRows();
					$result->finish();
				}

				$this->_execute( $stmts['column'] );

				if( $cntRows > 0 ) {
					$this->_status( sprintf( 'migrated (%1$d)', $cntRows ) );
				} else {
					$this->_status( 'OK' );
				}
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
		else
		{
			$this->_status( 'OK' );
		}

	}

}
