<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
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
		$this->info( 'Populating "fsname" column in media table', 'vv' );

		$this->db( 'db-media' )->update( 'mshop_media', ['fsname' => 'fs-media'], ['fsname' => ''] );
	}
}
