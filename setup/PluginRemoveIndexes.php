<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class PluginRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Plugin'];
	}


	public function up()
	{
		$this->info( 'Remove plugin indexes with siteid column first', 'vv' );

		$this->db( 'db-plugin' )
			->dropIndex( 'mshop_plugin', 'unq_msplu_sid_ty_prov' )
			->dropIndex( 'mshop_plugin', 'idx_msplu_sid_prov' )
			->dropIndex( 'mshop_plugin', 'idx_msplu_sid_status' )
			->dropIndex( 'mshop_plugin', 'idx_msplu_sid_label' )
			->dropIndex( 'mshop_plugin', 'idx_msplu_sid_pos' )
			->dropIndex( 'mshop_plugin_type', 'unq_mspluty_sid_dom_code' )
			->dropIndex( 'mshop_plugin_type', 'idx_mspluty_sid_status_pos' )
			->dropIndex( 'mshop_plugin_type', 'idx_mspluty_sid_label' )
			->dropIndex( 'mshop_plugin_type', 'idx_mspluty_sid_code' );
	}
}
