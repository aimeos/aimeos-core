<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


return array(

	'table' => array(

		'mshop_subscription' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_mssub_id' );
			$table->string( 'siteid' );
			$table->bigint( 'orderid' );
			$table->bigint( 'ordprodid' );
			$table->date( 'next' )->null( true );
			$table->date( 'end' )->null( true );
			$table->refid( 'productid' )->default( '' );
			$table->string( 'interval', 32 );
			$table->smallint( 'reason' )->null( true );
			$table->smallint( 'period' )->default( 0 );
			$table->smallint( 'status' )->default( 0 );
			$table->meta();

			$table->index( ['productid', 'period', 'siteid'], 'idx_mssub_pid_period_sid' );
			$table->index( ['next', 'status', 'siteid'], 'idx_mssub_next_stat_sid' );
			$table->index( ['ordprodid'], 'idx_mssub_opid' );
			$table->index( ['orderid'], 'idx_mssub_oid' );
		},
	),
);
