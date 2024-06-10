<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\Upscheme\Task;


class Test extends Base
{
	public function up()
	{
		$this->info( 'Creating test schema', 'vv' );

        $this->db( 'db-test' )->table( 'mshop_test', function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id();
			$table->string( 'siteid' );
			$table->string( 'key', 32 );
			$table->string( 'value' );
			$table->text( 'json' )->null( true );
			$table->meta();

			$table->index( ['key', 'siteid'], 'idx_mstes_key_sid' );
		} );
	}
}
