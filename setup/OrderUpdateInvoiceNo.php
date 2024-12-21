<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023-2024
 */


namespace Aimeos\Upscheme\Task;


class OrderUpdateInvoiceNo extends Base
{
	public function after() : array
	{
		return ['Locale', 'Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );
		$db2 = $this->db( 'db-locale' );

		if( !$db->hasColumn( 'mshop_order', 'invoiceno' ) || !$db2->hasColumn( 'mshop_locale_site', 'invoiceno' ) ) {
			return;
		}

		$this->info( 'Set invoice ID in order table if empty', 'vv' );

		$db->stmt()->update( 'mshop_order' )
			->set( $db->qi( 'invoiceno' ), $db->qi( 'id' ) )
			->where( $db->qi( 'invoiceno' ) . ' = \'\'' )
			->executeStatement();

		$result = $db->stmt()
			->select( 'MAX(' . $db->qi( 'id' ) . ') AS maxnum' )
			->from( 'mshop_order' )
			->executeQuery();

		while( $row = $result->fetchAssociative() )
		{
			$db2->stmt()->update( 'mshop_locale_site' )
				->set( 'invoiceno', '?' )
				->where( 'siteid = ?' )->andWhere( 'invoiceno = ?' )
				->setParameters( [$row['maxnum'], $row['siteid'], 1] )
				->executeStatement();
		}
	}
}
