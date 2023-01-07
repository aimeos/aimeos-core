<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class CustomerRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Customer'];
	}


	public function up()
	{
		$this->info( 'Remove customer indexes with siteid column first', 'vv' );

		$this->db( 'db-customer' )
			->dropIndex( 'mshop_customer', 'unq_mscus_sid_code' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_langid' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_last_first' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_post_addr1' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_post_city' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_city' )
			->dropIndex( 'mshop_customer', 'idx_mscus_sid_email' )
			->dropIndex( 'mshop_customer_address', 'idx_mscusad_sid_last_first' )
			->dropIndex( 'mshop_customer_address', 'idx_mscusad_sid_post_addr1' )
			->dropIndex( 'mshop_customer_address', 'idx_mscusad_sid_post_ci' )
			->dropIndex( 'mshop_customer_address', 'idx_mscusad_sid_city' )
			->dropIndex( 'mshop_customer_address', 'idx_mscusad_sid_email' )
			->dropIndex( 'mshop_customer_list', 'unq_mscusli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_customer_list_type', 'unq_mscuslity_sid_dom_code' )
			->dropIndex( 'mshop_customer_list_type', 'idx_mscuslity_sid_status_pos' )
			->dropIndex( 'mshop_customer_list_type', 'idx_mscuslity_sid_label' )
			->dropIndex( 'mshop_customer_list_type', 'idx_mscuslity_sid_code' )
			->dropIndex( 'mshop_customer_property', 'fk_mscuspr_key_sid' )
			->dropIndex( 'mshop_customer_property', 'unq_mcuspr_sid_ty_lid_value' )
			->dropIndex( 'mshop_customer_property_type', 'unq_mcusprty_sid_dom_code' )
			->dropIndex( 'mshop_customer_property_type', 'idx_mcusprty_sid_status_pos' )
			->dropIndex( 'mshop_customer_property_type', 'idx_mcusprty_sid_label' )
			->dropIndex( 'mshop_customer_property_type', 'idx_mcusprty_sid_code' );
	}
}
