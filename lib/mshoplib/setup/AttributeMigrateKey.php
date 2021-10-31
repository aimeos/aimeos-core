<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class AttributeMigrateKey extends Base
{
	public function before() : array
	{
		return ['Attribute'];
	}


	public function after() : array
	{
		return ['TypesMigrateColumns'];
	}


	public function up()
	{
		$this->info( 'Update attribute "key" columns', 'v' );

		$db = $this->db( 'db-attribute' );

		if( $db->hasColumn( 'mshop_attribute', 'key' ) )
		{
			$dbm = $this->context()->getDatabaseManager();
			$conn = $dbm->acquire( 'db-attribute' );

			$select = 'SELECT "id", "domain", "type", "code" FROM "mshop_attribute" WHERE "key" = \'\'';
			$update = 'UPDATE "mshop_attribute" SET "key" = ? WHERE "id" = ?';

			$stmt = $conn->create( $update );
			$result = $conn->create( $select )->execute();

			while( ( $row = $result->fetch() ) !== null )
			{
				$stmt->bind( 1, md5( $row['domain'] . '|' . $row['type'] . '|' . $row['code'] ) );
				$stmt->bind( 2, $row['id'] );
				$stmt->execute()->finish();
			}

			$dbm->release( $conn, 'db-attribute' );
		}
	}
}
