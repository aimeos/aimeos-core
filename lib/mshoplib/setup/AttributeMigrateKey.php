<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates key columns
 */
class AttributeMigrateKey extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update attribute "key" columns', 0 ); $this->status( '' );

		$schema = $this->getSchema( 'db-attribute' );

		$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

		if( $schema->tableExists( 'mshop_attribute' ) && $schema->columnExists( $table, 'key' )
			&& ( $item = $schema->getColumnDetails( 'mshop_attribute', 'key' ) ) && ( $item->getMaxLength() !== 32 )
		) {
			$dbm = $this->additional->getDatabaseManager();
			$conn = $dbm->acquire( $rname );

			$select = sprintf( 'SELECT "id", "domain", "type", "code" FROM "%1$s"', $table );
			$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

			$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
			$result = $conn->create( $select )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$stmt->bind( 1, md5( $row['domain'] . '|' . $row['type'] . '|' . $row['code'] ) );
				$stmt->bind( 2, $row['id'] );
				$stmt->execute()->finish();
			}

			$dbm->release( $conn, $rname );

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
