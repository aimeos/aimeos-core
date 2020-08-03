<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Ensures service codes are unique
 */
class ServiceUniqueCode extends \Aimeos\MW\Setup\Task\Base
{
	private $select = '
		SELECT "code" FROM "mshop_service" GROUP BY "code" HAVING COUNT("code") > 1
	';
	private $update = '
		UPDATE "mshop_service" SET "code"=? WHERE "code"=?
		AND "id" IN ( SELECT "id" FROM "mshop_service_type" WHERE "code"=\'delivery\' )
	';


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Renames all order tables if they exist.
	 */
	public function migrate()
	{
		$this->msg( 'Ensure unique codes in mshop_service', 0 );
		$schema = $this->getSchema( 'db-service' );
		$table = 'mshop_service';

		if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'code' ) )
		{
			$list = [];
			$conn = $this->acquire( 'db-service' );
			$result = $conn->create( $this->select )->execute();

			while( ( $row = $result->fetch() ) !== null ) {
				$list[] = $row['code'];
			}
			$result->finish();

			$stmt = $conn->create( $this->update );
			foreach( $list as $code )
			{
				$stmt->bind( 1, $code );
				$stmt->bind( 2, $code . '2' );
				$stmt->execute()->finish();
			}

			$this->release( $conn );

			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}
}
