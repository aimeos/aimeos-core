<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates the full text index on mshop_index_text.content for SQL Server
 */
class IndexCreateSQLSrvFulltext extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Creating full text index on "mshop_index_text.content" for SQL Server', 0 );

		$schema = $this->getSchema( 'db-product' );

		if( $schema->getName() === 'sqlsrv' && $schema->tableExists( 'mshop_index_text' ) )
		{
			if( !$this->getValue( 'SELECT SERVERPROPERTY(\'IsFullTextInstalled\') as prop', 'prop', 'db-product' ) ) {
				return $this->status( 'no' );
			}

			try
			{
				$sql = sprintf( '
					SELECT object_id FROM sys.fulltext_indexes
					WHERE object_id = OBJECT_ID(\'%1$s.dbo.mshop_index_text\')
				', $schema->getDBName() );

				$this->getValue( $sql, 'object_id', 'db-product' );
				return $this->status( 'OK' );
			}
			catch( \Aimeos\MW\Setup\Exception $e )
			{
				$sql = sprintf( '
					SELECT name FROM sys.indexes
					WHERE object_id = OBJECT_ID(\'%1$s.dbo.mshop_index_text\') AND is_primary_key = 1
				', $schema->getDBName() );
				$name = $this->getValue( $sql, 'name', 'db-product' );

				$this->execute( 'CREATE FULLTEXT CATALOG "aimeos"', 'db-product' );
				$this->execute( '
					CREATE FULLTEXT INDEX ON "mshop_index_text" ("content")
					KEY INDEX ' . $name . ' ON "aimeos"
				', 'db-product' );

				return $this->status( 'done' );
			}
		}

		$this->status( 'OK' );
	}
}
