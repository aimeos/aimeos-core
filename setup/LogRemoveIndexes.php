<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class LogRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Log'];
	}


	public function up()
	{
		$this->info( 'Remove log indexes with siteid column first', 'vv' );

		$this->db( 'db-log' )->dropIndex( 'madmin_log', 'idx_malog_sid_time_facility_prio' );
	}
}
