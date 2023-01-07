<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class TextRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Text'];
	}


	public function up()
	{
		$this->info( 'Remove text indexes with siteid column first', 'vv' );

		$this->db( 'db-text' )
			->dropIndex( 'mshop_text', 'idx_mstex_sid_domain_status' )
			->dropIndex( 'mshop_text', 'idx_mstex_sid_domain_langid' )
			->dropIndex( 'mshop_text', 'idx_mstex_sid_dom_label' )
			->dropIndex( 'mshop_text_type', 'unq_mstexty_sid_dom_code' )
			->dropIndex( 'mshop_text_type', 'idx_mstexty_sid_status_pos' )
			->dropIndex( 'mshop_text_type', 'idx_mstexty_sid_label' )
			->dropIndex( 'mshop_text_type', 'idx_mstexty_sid_code' )
			->dropIndex( 'mshop_text_list', 'unq_mstexli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_text_list_type', 'unq_mstexlity_sid_dom_code' )
			->dropIndex( 'mshop_text_list_type', 'idx_mstexlity_sid_status_pos' )
			->dropIndex( 'mshop_text_list_type', 'idx_mstexlity_sid_label' )
			->dropIndex( 'mshop_text_list_type', 'idx_mstexlity_sid_code' );
	}
}
