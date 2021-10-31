<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


return array(

	'table' => array(

		'mshop_subscription' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_mssub_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->bigint( 'ordprodid' );
			$table->date( 'next' )->null( true );
			$table->date( 'end' )->null( true );
			$table->refid( 'productid' )->default( '' );
			$table->string( 'interval', 32 );
			$table->smallint( 'reason' )->null( true );
			$table->smallint( 'period' )->default( 0 );
			$table->smallint( 'status' )->default( 0 );
			$table->meta();

			$table->index( ['siteid', 'next', 'status'], 'idx_mssub_sid_next_stat' );
			$table->index( ['siteid', 'baseid'], 'idx_mssub_sid_baseid' );
			$table->index( ['siteid', 'ordprodid'], 'idx_mssub_sid_opid' );
			$table->index( ['siteid', 'productid', 'period'], 'idx_mssub_sid_pid_period' );
		},
	),
);
