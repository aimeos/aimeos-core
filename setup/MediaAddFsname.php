<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2025
 */


namespace Aimeos\Upscheme\Task;


class MediaAddFsname extends Base
{
	public function after() : array
	{
		return ['Media'];
	}


	public function up()
	{
		$this->info( 'Add values for empty "fsname" column', 'vv' );

		$this->db( 'db-media' )->update( 'mshop_media', ['fsname' => 'fs-media'], ['fsname' => ''] );
	}
}
