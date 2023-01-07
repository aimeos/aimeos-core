<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class AttributeRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Attribute'];
	}


	public function up()
	{
		$this->info( 'Remove attribute indexes with siteid column first', 'vv' );

		$this->db( 'db-attribute' )
			->dropIndex( 'mshop_attribute', 'unq_msatt_dom_sid_type_code' )
			->dropIndex( 'mshop_attribute', 'idx_msatt_sid_status' )
			->dropIndex( 'mshop_attribute', 'idx_msatt_sid_label' )
			->dropIndex( 'mshop_attribute', 'idx_msatt_sid_code' )
			->dropIndex( 'mshop_attribute', 'idx_msatt_sid_type' )
			->dropIndex( 'mshop_attribute_type', 'unq_msattty_sid_dom_code' )
			->dropIndex( 'mshop_attribute_type', 'idx_msattty_sid_status_pos' )
			->dropIndex( 'mshop_attribute_type', 'idx_msattty_sid_label' )
			->dropIndex( 'mshop_attribute_type', 'idx_msattty_sid_code' )
			->dropIndex( 'mshop_attribute_list', 'unq_msattli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_attribute_list_type', 'unq_msattlity_sid_dom_code' )
			->dropIndex( 'mshop_attribute_list_type', 'idx_msattlity_sid_status_pos' )
			->dropIndex( 'mshop_attribute_list_type', 'idx_msattlity_sid_label' )
			->dropIndex( 'mshop_attribute_list_type', 'idx_msattlity_sid_code' )
			->dropIndex( 'mshop_attribute_property', 'fk_msattpr_key_sid' )
			->dropIndex( 'mshop_attribute_property', 'unq_msattpr_sid_ty_lid_value' )
			->dropIndex( 'mshop_attribute_property_type', 'unq_msattprty_sid_dom_code' )
			->dropIndex( 'mshop_attribute_property_type', 'idx_msattprty_sid_status_pos' )
			->dropIndex( 'mshop_attribute_property_type', 'idx_msattprty_sid_label' )
			->dropIndex( 'mshop_attribute_property_type', 'idx_msattprty_sid_code' );
	}
}
