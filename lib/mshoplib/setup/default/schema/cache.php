<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'madmin_cache' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'id' )->primary( 'pk_macac_id' );
			$table->datetime( 'expire' )->null( true );
			$table->text( 'value', 0x1ffff );

			$table->index( ['expire'], 'idx_majob_expire' );
		},

		'madmin_cache_tag' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'tid' );
			$table->string( 'tname' );

			$table->unique( ['tid', 'tname'], 'unq_macacta_tid_tname' );
			$table->index( ['tid'], 'fk_macac_tid' );

			$table->foreign( 'tid', 'madmin_cache', 'id', 'fk_macacta_tid' );
		},
	),
);
