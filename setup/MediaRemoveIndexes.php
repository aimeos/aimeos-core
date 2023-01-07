<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class MediaRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Media'];
	}


	public function up()
	{
		$this->info( 'Remove media indexes with siteid column first', 'vv' );

		$this->db( 'db-media' )
			->dropIndex( 'mshop_media', 'idx_msmed_sid_dom_langid' )
			->dropIndex( 'mshop_media', 'idx_msmed_sid_dom_label' )
			->dropIndex( 'mshop_media', 'idx_msmed_sid_dom_mime' )
			->dropIndex( 'mshop_media', 'idx_msmed_sid_dom_link' )
			->dropIndex( 'mshop_media_type', 'unq_msmedty_sid_dom_code' )
			->dropIndex( 'mshop_media_type', 'idx_msmedty_sid_status_pos' )
			->dropIndex( 'mshop_media_type', 'idx_msmedty_sid_label' )
			->dropIndex( 'mshop_media_type', 'idx_msmedty_sid_code' )
			->dropIndex( 'mshop_media_list', 'unq_msmedli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_media_list_type', 'unq_msmedlity_sid_dom_code' )
			->dropIndex( 'mshop_media_list_type', 'idx_msmedlity_sid_status_pos' )
			->dropIndex( 'mshop_media_list_type', 'idx_msmedlity_sid_label' )
			->dropIndex( 'mshop_media_list_type', 'idx_msmedlity_sid_code' )
			->dropIndex( 'mshop_media_property', 'fk_msmedpr_key_sid' )
			->dropIndex( 'mshop_media_property', 'unq_msmedpr_sid_ty_lid_value' )
			->dropIndex( 'mshop_media_property_type', 'unq_msmedprty_sid_dom_code' )
			->dropIndex( 'mshop_media_property_type', 'idx_msmedprty_sid_status_pos' )
			->dropIndex( 'mshop_media_property_type', 'idx_msmedprty_sid_label' )
			->dropIndex( 'mshop_media_property_type', 'idx_msmedprty_sid_code' );
	}
}
