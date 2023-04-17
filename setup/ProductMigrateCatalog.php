<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class ProductMigrateCatalog extends Base
{
	public function after() : array
	{
		return ['Catalog', 'Product', 'TablesMigrateListsKey'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );
		$db2 = $this->db( 'db-catalog' );

		if( !$db->hasTable( 'mshop_product_list' ) || !$db2->hasTable( 'mshop_catalog_list' ) ) {
			return;
		}

		$this->info( 'Migrating category references to product domain', 'vv' );

		$insert = $db->stmt()->insert( 'mshop_product_list' )->values( [
			$db->qi( 'parentid' ) => '?', $db->qi( 'siteid' ) => '?', $db->qi( 'key' ) => '?', $db->qi( 'domain' ) => '?',
			$db->qi( 'type' ) => '?', $db->qi( 'start' ) => '?', $db->qi( 'end' ) => '?', $db->qi( 'config' ) => '?',
			$db->qi( 'status' ) => '?', $db->qi( 'pos' ) => '?', $db->qi( 'refid' ) => '?', $db->qi( 'ctime' ) => '?',
			$db->qi( 'mtime' ) => '?', $db->qi( 'editor' ) => '?'
		] );

		$failed = 0;

		do
		{
			$ids = [];

			$result = $db2->stmt()->select( '*' )->from( 'mshop_catalog_list' )
				->where( $db2->qi( 'key' ) . " LIKE 'product|%'" )
				->setMaxResults( 1000 )->execute();

			while( $row = $result->fetchAssociative() )
			{
				try
				{
					$insert->setParameter( 0, $row['refid'] )
						->setParameter( 1, $row['siteid'] )
						->setParameter( 2, 'catalog|' . $row['type'] . '|' . $row['parentid'] )
						->setParameter( 3, 'catalog' )
						->setParameter( 4, $row['type'] )
						->setParameter( 5, $row['start'] )
						->setParameter( 6, $row['end'] )
						->setParameter( 7, $row['config'] )
						->setParameter( 8, $row['status'] )
						->setParameter( 9, $row['pos'] )
						->setParameter( 10, $row['parentid'] )
						->setParameter( 11, $row['ctime'] )
						->setParameter( 12, $row['mtime'] )
						->setParameter( 13, $row['editor'] )
						->execute();

					$ids[] = $row['id'];
				}
				catch( \Exception $e )
				{
					$failed++;
				}
			}

			if( $count = count( $ids ) )
			{
				$db2->stmt()->delete( 'mshop_catalog_list' )
					->where( $db2->qi( 'id' ) . ' IN (' . join( ',', $ids ) . ')' )
					->execute();
			}
		}
		while( $count >= 1000 );

		if( $failed ) {
			$this->info( 'Failed product category migrations: ' . $failed );
		}
	}
}
