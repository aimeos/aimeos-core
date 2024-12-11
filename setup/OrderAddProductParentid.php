<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Upscheme\Task;


class OrderAddProductParentid extends Base
{
	public function after() : array
	{
		return ['Product'];
	}


	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_product', 'parentprodid' ) ) {
			return;
		}

		$this->info( 'Separate product ID and parent ID in order base product table', 'vv' );

		$table = $db->table( 'mshop_order_base_product' );
		$table->refid( 'parentprodid' )->up();

		$db->stmt()->update( 'mshop_order_base_product' )
			->set( 'parentprodid', 'prodid' )
			->where( 'type = \'select\'' )
			->executeStatement();

		$result = $db->stmt()->select( 'siteid', 'prodcode' )
			->from( 'mshop_order_base_product' )
			->where( 'type = \'select\'' )
			->executeQuery();

		$used = [];
		$db2 = $this->db( 'db-order', true );
		$proddb = $this->db( 'db-product' );

		while( $row = $result->fetchAssociative() )
		{
			if( isset( $used[$row['siteid']][$row['code']] ) ) {
				continue;
			}

			$rows = $proddb->select( 'mshop_product', ['siteid' => $row['siteid'], 'code' => $row['prodcode']] );

			foreach( $rows as $product )
			{
				$db2->stmt()->update( 'mshop_order_base_product' )
					->set( 'prodid', '?' )
					->where( 'siteid = ?' )->andWhere( 'prodcode = ?' )
					->setParameters( [$product['id'], $product['siteid'], $product['code']] )
					->executeStatement();
			}

			$used[$row['siteid']][$row['code']] = true;
		}
	}
}
