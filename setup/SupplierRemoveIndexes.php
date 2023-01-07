<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class SupplierRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Supplier'];
	}


	public function up()
	{
		$this->info( 'Remove supplier indexes with siteid column first', 'vv' );

		$this->db( 'db-supplier' )
			->dropIndex( 'mshop_supplier', 'unq_mssup_sid_code' )
			->dropIndex( 'mshop_supplier', 'idx_mssup_sid_label' )
			->dropIndex( 'mshop_supplier_address', 'idx_mssupad_sid_rid' )
			->dropIndex( 'mshop_supplier_type', 'unq_mssupty_sid_dom_code' )
			->dropIndex( 'mshop_supplier_type', 'idx_mssupty_sid_status_pos' )
			->dropIndex( 'mshop_supplier_type', 'idx_mssupty_sid_label' )
			->dropIndex( 'mshop_supplier_type', 'idx_mssupty_sid_code' )
			->dropIndex( 'mshop_supplier_list', 'unq_mssupli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_supplier_list', 'idx_mssupli_pid_dm_sid_pos_rid' )
			->dropIndex( 'mshop_supplier_list', 'idx_mssupli_rid_dom_sid_ty' )
			->dropIndex( 'mshop_supplier_list_type', 'unq_mssuplity_sid_dom_code' )
			->dropIndex( 'mshop_supplier_list_type', 'idx_mssuplity_sid_status_pos' )
			->dropIndex( 'mshop_supplier_list_type', 'idx_mssuplity_sid_label' )
			->dropIndex( 'mshop_supplier_list_type', 'idx_mssuplity_sid_code' );
	}
}
