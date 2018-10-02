<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates salutation values in order base address.
 */
class OrderAddressMigrateSalutation extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_order_base_address" SET "salutation" = \'unknown\' WHERE "salutation" = \'0\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'company\' WHERE "salutation"=\'1\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'mrs\' WHERE "salutation"=\'2\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'miss\' WHERE "salutation"=\'3\'',
		'UPDATE "mshop_order_base_address" SET "salutation" = \'mr\' WHERE "salutation"=\'4\''
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	public function migrate()
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
		$this->msg( 'Migrating order address salutations', 0 ); $this->status( '' );
		$table = 'mshop_order_base_address';
		$cntRows = 0;

		$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

		if( $this->schema->tableExists( $table ) === true )
		{
			$conn = $this->acquire( 'db-order' );

			foreach( $stmts as $sql )
			{
				$stmt = $conn->create( $sql );
				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}

			$this->release( $conn, 'db-order' );

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
