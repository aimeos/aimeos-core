<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Migrates the preview URLs in media table
 */
class MediaMigratePreview extends \Aimeos\MW\Setup\Task\Base
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
		$dbdomain = 'db-media';
		$this->msg( 'Migrating preview column in media table', 0 );

		if( $this->getSchema( $dbdomain )->tableExists( 'mshop_media' ) === false )
		{
			$this->status( 'OK' );
			return;
		}

		$start = 0;
		$conn = $this->acquire( $dbdomain );
		$select = 'SELECT "id", "preview" FROM "mshop_media" WHERE "preview" NOT LIKE \'{%\' LIMIT 1000 OFFSET :offset';
		$update = 'UPDATE "mshop_media" SET "preview" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );

		do
		{
			$count = 0;
			$map = [];
			$sql = str_replace( ':offset', $start, $select );
			$result = $conn->create( $sql )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$map[$row['id']] = $row['preview'];
				$count++;
			}

			foreach( $map as $id => $preview )
			{
				$stmt->bind( 1, json_encode( ['1' => $preview], JSON_FORCE_OBJECT ) );
				$stmt->bind( 2, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}

			$start += $count;
		}
		while( $count === 1000 );

		$this->release( $conn, $dbdomain );

		$this->status( 'done' );
	}
}
