<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameProductSupplier extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->info( 'Rename "suppliername" to "vendor" in "mshop_order_base_product" table', 'vv' );

		$db = $this->db( 'db-order' );
		$db->renameColumn( 'mshop_order_base_product', 'suppliername', 'vendor' );
		$db->dropColumn( 'mshop_order_base_product', ['supplierid', 'suppliercode'] );
	}
}
