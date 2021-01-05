<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the path in job table
 */
class JobMigratePath extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Migrate database schema
	 */
	public function migrate()
	{
		$this->msg( 'Migrating path in job table', 0 );

		$dbdomain = 'db-job';
		$schema = $this->getSchema( $dbdomain );

		if( $schema->tableExists( 'madmin_job' ) === false
			|| $schema->columnExists( 'madmin_job', 'result' ) === false
		) {
			$this->status( 'OK' );
			return;
		}

		$conn = $this->acquire( $dbdomain );
		$select = 'SELECT "id", "result" FROM "madmin_job" WHERE "result" LIKE \'{%\'';
		$update = 'UPDATE "madmin_job" SET "path" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$stmt->bind( 1, $row['result']['file'] ?? '' );
			$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}

		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
