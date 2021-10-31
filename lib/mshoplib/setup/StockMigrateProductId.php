<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Upscheme\Task;


class StockMigrateProductId extends Base
{
	public function before() : array
	{
		return ['Stock'];
	}


	public function up()
	{
		$db = $this->db( 'db-stock' );

		if( !$db->hasTable( 'mshop_stock' ) ) {
			return;
		}

		$this->info( 'Migrate product code to product ID in stock table', 'v' );

		if( !$db->hasColumn( 'mshop_stock', 'prodid' ) )
		{
			$db->table( 'mshop_stock' )->refid( 'prodid' )->up();
			$db->exec( 'UPDATE mshop_stock SET prodid = (
				SELECT id FROM mshop_product AS p WHERE p.code = productcode AND p.siteid = siteid LIMIT 1
			)' );
		}

		$db->exec( 'DELETE FROM mshop_stock WHERE prodid IS NULL' );

		$db->dropIndex( 'mshop_stock', 'unq_mssto_sid_pcode_ty' );
		$db->dropColumn( 'mshop_stock', 'productcode' );
	}
}
