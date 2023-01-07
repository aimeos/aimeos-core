<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class ReviewRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Review'];
	}


	public function up()
	{
		$this->info( 'Remove review indexes with siteid column first', 'vv' );

		$this->db( 'db-review' )
			->dropIndex( 'mshop_review', 'unq_msrev_sid_cid_dom_rid' )
			->dropIndex( 'mshop_review', 'idx_msrev_sid_dom_rid_sta_ct' )
			->dropIndex( 'mshop_review', 'idx_msrev_sid_dom_rid_sta_rate' )
			->dropIndex( 'mshop_review', 'idx_msrev_sid_dom_cid_mt' )
			->dropIndex( 'mshop_review', 'idx_msrev_sid_rate_dom' );
	}
}
