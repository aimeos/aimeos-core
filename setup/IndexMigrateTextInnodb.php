<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */


namespace Aimeos\Upscheme\Task;


class IndexMigrateTextInnodb extends Base
{
	public function before() : array
	{
		return ['TablesMigrateSiteid'];
	}


	public function up()
	{
		$db = $this->db( 'db-product' );

		if( !$db->hasTable( 'mshop_index_text' ) || $db->table( 'mshop_index_text' )->opt( 'engine' ) === 'InnoDB' ) {
			return;
		}

		$this->info( 'Migrate mshop_index_text table engine to InnoDB', 'vv' );

		$db->for( ['mariadb', 'mysql'], 'ALTER TABLE mshop_index_text ENGINE=InnoDB' );
	}
}
