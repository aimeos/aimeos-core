<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Upscheme\Task;


class ServiceUniqueCode extends Base
{
	private $select = '
		SELECT "code" FROM "mshop_service" GROUP BY "code" HAVING COUNT("code") > 1
	';
	private $update = '
		UPDATE "mshop_service" SET "code"=? WHERE "code"=?
		AND "id" IN ( SELECT "id" FROM "mshop_service_type" WHERE "code"=\'delivery\' )
	';


	public function before() : array
	{
		return ['Service'];
	}


	public function up()
	{
		$db = $this->db( 'db-service' );

		if( !$db->hasColumn( 'mshop_service', 'code' ) ) {
			return;
		}

		$this->info( 'Ensure unique codes in mshop_service', 'v' );

		$list = [];
		$dbm = $this->context()->db();
		$conn = $dbm->acquire( 'db-service' );
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

		$dbm->release( $conn, 'db-service' );
	}
}
