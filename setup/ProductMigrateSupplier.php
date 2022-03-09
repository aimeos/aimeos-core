<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


namespace Aimeos\Upscheme\Task;


class ProductMigrateSupplier extends Base
{
	public function before() : array
	{
		return ['Product', 'Supplier'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );
		$db2 = $this->db( 'db-supplier' );

		if( !$db->hasTable( 'mshop_product_list' ) || !$db2->hasTable( 'mshop_supplier_list' ) ) {
			return;
		}

		$this->info( 'Migrating supplier references to product domain', 'v' );

		$insert = $db->stmt()->insert( 'mshop_product_list' )->values( [
			'parentid' => '?', 'siteid' => '?', 'key' => '?', 'domain' => '?', 'type' => '?',
			'start' => '?', 'end' => '?', 'config' => '?', 'status' => '?', 'pos' => '?',
			'refid' => '?', 'ctime' => '?', 'mtime' => '?', 'editor' => '?'
		] );

		do
		{
			$result = $db2->stmt()->select( '*' )->from( 'mshop_supplier_list' )
				->where( $db2->qi( 'key' ) . " LIKE 'product|%'" )
				->setMaxResults( 1000 )->execute();

			$ids = [];
			while( $row = $result->fetchAssociative() )
			{
				$ids[] = $row['id'];
				$insert->setParameter( 0, $row['refid'] )
					->setParameter( 1, $row['siteid'] )
					->setParameter( 2, 'supplier|' . $row['type'] . '|' . $row['parentid'] )
					->setParameter( 3, 'supplier' )
					->setParameter( 4, $row['type'] )
					->setParameter( 5, $row['start'] )
					->setParameter( 6, $row['end'] )
					->setParameter( 7, $row['config'] )
					->setParameter( 8, $row['status'] )
					->setParameter( 9, $row['pos'] )
					->setParameter( 10, $row['parentid'] )
					->setParameter( 11, $row['ctime'] )
					->setParameter( 12, $row['mtime'] )
					->setParameter( 13, $row['editor'] );
			}

			if( $count = count( $ids ) )
			{
				$db2->stmt()->delete( 'mshop_supplier_list' )
					->where( $db2->qi( 'id' ) . ' IN (' . join( ',', $ids ) . ')' )
					->execute();
			}
		}
		while( $count >= 1000 );
	}
}
