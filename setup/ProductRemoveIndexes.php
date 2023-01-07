<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class ProductRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Product'];
	}


	public function up()
	{
		$this->info( 'Remove product indexes with siteid column first', 'vv' );

		$this->db( 'db-product' )
			->dropIndex( 'mshop_product', 'unq_mspro_siteid_code' )
			->dropIndex( 'mshop_product', 'idx_mspro_id_sid_stat_st_end_rt' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_stat_st_end_rt' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_rating' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_label' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_start' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_type' )
			->dropIndex( 'mshop_product', 'idx_mspro_sid_end' )
			->dropIndex( 'mshop_product_type', 'unq_msproty_sid_dom_code' )
			->dropIndex( 'mshop_product_type', 'idx_msproty_sid_status_pos' )
			->dropIndex( 'mshop_product_type', 'idx_msproty_sid_label' )
			->dropIndex( 'mshop_product_type', 'idx_msproty_sid_code' )
			->dropIndex( 'mshop_product_list', 'unq_msproli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_product_list_type', 'unq_msprolity_sid_dom_code' )
			->dropIndex( 'mshop_product_list_type', 'idx_msprolity_sid_status_pos' )
			->dropIndex( 'mshop_product_list_type', 'idx_msprolity_sid_label' )
			->dropIndex( 'mshop_product_list_type', 'idx_msprolity_sid_code' )
			->dropIndex( 'mshop_product_property', 'fk_mspropr_key_sid' )
			->dropIndex( 'mshop_product_property', 'unq_mspropr_sid_ty_lid_value' )
			->dropIndex( 'mshop_product_property_type', 'unq_msproprty_sid_dom_code' )
			->dropIndex( 'mshop_product_property_type', 'idx_msproprty_sid_status_pos' )
			->dropIndex( 'mshop_product_property_type', 'idx_msproprty_sid_label' )
			->dropIndex( 'mshop_product_property_type', 'idx_msproprty_sid_code' );
	}
}
