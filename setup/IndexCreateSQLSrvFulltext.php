<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */


namespace Aimeos\Upscheme\Task;


class IndexCreateSQLSrvFulltext extends Base
{
	public function after() : array
	{
		return ['Index'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( $db->type() !== 'sqlserver' || !$db->hasTable( 'mshop_index_text' )
			|| !$db->stmt()->select( 'SERVERPROPERTY(\'IsFullTextInstalled\')' )->setMaxResults( 1 )->executeQuery()->fetchOne()
		) {
			return;
		}

		$this->info( 'Creating full text index on "mshop_index_text.content" for SQL Server', 'vv' );

		$result = $db->stmt()->select( 'object_id' )
			->from( 'sys.fulltext_indexes' )
			->where( sprintf( 'object_id = OBJECT_ID(\'%1$s.dbo.mshop_index_text\')', $db->name() ) )
			->setMaxResults( 1 )->executeQuery()->fetchOne();

		if( !$result )
		{
			$name = $db->stmt()->select( 'name' )
				->from( 'sys.indexes' )
				->where( sprintf( 'object_id = OBJECT_ID(\'%1$s.dbo.mshop_index_text\') AND is_primary_key = 1', $db->name() ) )
				->setMaxResults( 1 )->executeQuery()->fetchOne();

			$db->for( 'sqlserver', 'CREATE FULLTEXT CATALOG "aimeos"' );
			$db->for( 'sqlserver', 'CREATE FULLTEXT INDEX ON "mshop_index_text" ("content") KEY INDEX ' . $name . ' ON "aimeos"' );
		}
	}
}
