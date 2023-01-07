<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class JobRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Job'];
	}


	public function up()
	{
		$this->info( 'Remove job indexes with siteid column first', 'vv' );

		$this->db( 'db-job' )
			->dropIndex( 'madmin_job', 'idx_majob_sid_ctime' )
			->dropIndex( 'madmin_job', 'idx_majob_sid_status' );
	}
}
