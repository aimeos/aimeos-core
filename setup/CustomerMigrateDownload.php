<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Upscheme\Task;


class CustomerMigrateDownload extends Base
{
	public function after() : array
	{
		return ['Customer'];
	}


	public function up()
	{
		$db = $this->db( 'db-customer' );

		if( !$db->hasTable( 'mshop_customer_list' ) ) {
			return;
		}

		$this->info( 'Migrate download counters in mshop_customer_list', 'vv' );

		$result = $db->stmt()->select( 'id', 'type', 'refid' )->from( 'mshop_customer_list' )
			->where( $db->qi( 'domain' ) . ' = ? AND ' . $db->qi( 'type' ) . ' = ?' )
			->setParameter( 0, 'order' )->setParameter( 1, 'download' )
			->executeQuery();

		$db2 = $this->db( 'db-customer', true );

		while( $row = $result->fetchAssociative() )
		{
			$db2->update(
				'mshop_customer_list',
				['key' => 'order/product/attribute|' . $row['type'] . '|' . $row['refid'], 'domain' => 'order/product/attribute'],
				['id' => $row['id']]
			);
		}

		$db2->close();
	}
}
