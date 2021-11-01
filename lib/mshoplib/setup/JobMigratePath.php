<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class JobMigratePath extends Base
{
	public function after() : array
	{
		return ['Job'];
	}


	public function up()
	{
		$db = $this->db( 'db-job' );

		if( !$db->hasColumn( 'madmin_job', 'result' ) ) {
			return;
		}

		$this->info( 'Migrating path in job table', 'v' );

		$dbm = $this->context()->getDatabaseManager();
		$conn = $dbm->acquire( 'db-job' );

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

		$dbm->release( $conn, 'db-job' );
	}
}
