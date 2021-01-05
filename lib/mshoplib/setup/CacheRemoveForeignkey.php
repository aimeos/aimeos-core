<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Remove old foreign key constraint from madmin_cache
 */
class CacheRemoveForeignkey extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMAdmin'];
	}


	/**
	 * Creates the MAdmin tables
	 */
	public function migrate()
	{
		$this->msg( 'Remove foreign key "fk_macac_tid_tsid" from "madmin_cache_tag"' );

		$status = 'OK';
		$conn = $this->acquire( 'db-cache' );
		$dbal = $conn->getRawObject();

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}

		$dbalManager = $dbal->getSchemaManager();

		if( $dbalManager->tablesExist( ['madmin_cache_tag'] ) )
		{
			$table = $dbalManager->listTableDetails( 'madmin_cache_tag' );

			if( $table->hasForeignKey( 'fk_macac_tid_tsid' ) )
			{
				$platform = $dbal->getDatabasePlatform();
				$config = $dbalManager->createSchemaConfig();

				$newTable = clone $table;
				$newTable->removeForeignKey( 'fk_macac_tid_tsid' );

				$oldSchema = new \Doctrine\DBAL\Schema\Schema( [$table], [], $config );
				$newSchema = new \Doctrine\DBAL\Schema\Schema( [$newTable], [], $config );
				$schemaDiff = \Doctrine\DBAL\Schema\Comparator::compareSchemas( $oldSchema, $newSchema );

				$this->executeList( $schemaDiff->toSaveSql( $platform ) );

				$status = 'done';
			}
		}

		$this->release( $conn, 'db-cache' );

		$this->status( $status );
	}
}
