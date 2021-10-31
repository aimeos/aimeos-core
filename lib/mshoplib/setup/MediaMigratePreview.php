<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class MediaMigratePreview extends Base
{
	public function after() : array
	{
		return ['Media'];
	}


	public function up()
	{
		$db = $this->db( 'db-media' );

		if( !$db->hasTable( 'mshop_media' ) ) {
			return;
		}

		$this->info( 'Migrating preview column in media table', 'v' );

		$dbm = $this->context()->db();
		$conn = $dbm->acquire( 'db-media' );

		$select = 'SELECT "id", "preview" FROM "mshop_media" WHERE "preview" NOT LIKE \'{%\'';
		$update = 'UPDATE "mshop_media" SET "preview" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update );
		$result = $conn->create( $select )->execute();

		while( ( $row = $result->fetch() ) !== null )
		{
			$stmt->bind( 1, json_encode( ['1' => $row['preview']], JSON_FORCE_OBJECT ) );
			$stmt->bind( 2, $row['id'], \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();
		}

		$dbm->release( $conn, 'db-media' );
	}
}
