<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class PriceRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Price'];
	}


	public function up()
	{
		$this->info( 'Remove price indexes with siteid column first', 'vv' );

		$this->db( 'db-price' )
			->dropIndex( 'mshop_price', 'idx_mspri_sid_dom_currid' )
			->dropIndex( 'mshop_price', 'idx_mspri_sid_dom_quantity' )
			->dropIndex( 'mshop_price', 'idx_mspri_sid_dom_value' )
			->dropIndex( 'mshop_price', 'idx_mspri_sid_dom_costs' )
			->dropIndex( 'mshop_price', 'idx_mspri_sid_dom_rebate' )
			->dropIndex( 'mshop_price_type', 'unq_msprity_sid_dom_code' )
			->dropIndex( 'mshop_price_type', 'idx_msprity_sid_status_pos' )
			->dropIndex( 'mshop_price_type', 'idx_msprity_sid_label' )
			->dropIndex( 'mshop_price_type', 'idx_msprity_sid_code' )
			->dropIndex( 'mshop_price_list', 'unq_msprili_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_price_list_type', 'unq_msprility_sid_dom_code' )
			->dropIndex( 'mshop_price_list_type', 'idx_msprility_sid_status_pos' )
			->dropIndex( 'mshop_price_list_type', 'idx_msprility_sid_label' )
			->dropIndex( 'mshop_price_list_type', 'idx_msprility_sid_code' )
			->dropIndex( 'mshop_price_property', 'fk_mspripr_key_sid' )
			->dropIndex( 'mshop_price_property', 'unq_mspripr_sid_ty_lid_value' )
			->dropIndex( 'mshop_price_property_type', 'unq_mspriprty_sid_dom_code' )
			->dropIndex( 'mshop_price_property_type', 'idx_mspriprty_sid_status_pos' )
			->dropIndex( 'mshop_price_property_type', 'idx_mspriprty_sid_label' )
			->dropIndex( 'mshop_price_property_type', 'idx_mspriprty_sid_code' );
	}
}
