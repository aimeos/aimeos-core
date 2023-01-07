<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class CatalogRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Catalog'];
	}


	public function up()
	{
		$this->info( 'Remove catalog indexes with siteid column first', 'vv' );

		$this->db( 'db-catalog' )
			->dropIndex( 'mshop_catalog', 'unq_mscat_sid_code' )
			->dropIndex( 'mshop_catalog', 'idx_mscat_sid_nlt_nrt_lvl_pid' )
			->dropIndex( 'mshop_catalog', 'idx_mscat_sid_status' )
			->dropIndex( 'mshop_catalog_list', 'unq_mscatli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_catalog_list', 'idx_mscatli_pid_dm_sid_pos_rid' )
			->dropIndex( 'mshop_catalog_list', 'idx_mscatli_rid_dom_sid_ty' )
			->dropIndex( 'mshop_catalog_list', 'idx_mscatli_pid_dm_pos_rid_sid' )
			->dropIndex( 'mshop_catalog_list', 'idx_mscatli_rid_dom_ty_sid' )
			->dropIndex( 'mshop_catalog_list_type', 'unq_mscatlity_sid_dom_code' )
			->dropIndex( 'mshop_catalog_list_type', 'idx_mscatlity_sid_status_pos' )
			->dropIndex( 'mshop_catalog_list_type', 'idx_mscatlity_sid_label' )
			->dropIndex( 'mshop_catalog_list_type', 'idx_mscatlity_sid_code' );
	}
}
