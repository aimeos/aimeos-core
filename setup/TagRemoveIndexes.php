<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class TagRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Tag'];
	}


	public function up()
	{
		$this->info( 'Remove tag indexes with siteid column first', 'vv' );

		$this->db( 'db-tag' )
			->dropIndex( 'mshop_tag', 'unq_mstag_sid_dom_ty_lid_lab' )
			->dropIndex( 'mshop_tag', 'idx_mstag_sid_dom_langid' )
			->dropIndex( 'mshop_tag', 'idx_mstag_sid_dom_label' )
			->dropIndex( 'mshop_tag_type', 'unq_mstagty_sid_dom_code' )
			->dropIndex( 'mshop_tag_type', 'idx_mstagty_sid_status_pos' )
			->dropIndex( 'mshop_tag_type', 'idx_mstagty_sid_label' )
			->dropIndex( 'mshop_tag_type', 'idx_mstagty_sid_code' );
	}
}
