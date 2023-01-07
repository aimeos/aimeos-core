<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class ServiceRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Service'];
	}


	public function up()
	{
		$this->info( 'Remove service indexes with siteid column first', 'vv' );

		$this->db( 'db-service' )
			->dropIndex( 'mshop_service', 'unq_msser_siteid_code' )
			->dropIndex( 'mshop_service', 'idx_msser_sid_stat_start_end' )
			->dropIndex( 'mshop_service', 'idx_msser_sid_prov' )
			->dropIndex( 'mshop_service', 'idx_msser_sid_code' )
			->dropIndex( 'mshop_service', 'idx_msser_sid_label' )
			->dropIndex( 'mshop_service', 'idx_msser_sid_pos' )
			->dropIndex( 'mshop_service_type', 'unq_msserty_sid_dom_code' )
			->dropIndex( 'mshop_service_type', 'idx_msserty_sid_status_pos' )
			->dropIndex( 'mshop_service_type', 'idx_msserty_sid_label' )
			->dropIndex( 'mshop_service_type', 'idx_msserty_sid_code' )
			->dropIndex( 'mshop_service_list', 'unq_msserli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_service_list_type', 'unq_msserlity_sid_dom_code' )
			->dropIndex( 'mshop_service_list_type', 'idx_msserlity_sid_status_pos' )
			->dropIndex( 'mshop_service_list_type', 'idx_msserlity_sid_label' )
			->dropIndex( 'mshop_service_list_type', 'idx_msserlity_sid_code' );
	}
}
