<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'madmin_log' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_mslog_id' );
			$table->string( 'siteid' )->default( '' );
			$table->datetime( 'timestamp' );
			$table->smallint( 'priority' )->default( 3 );
			$table->string( 'facility', 32 )->default( '' );
			$table->string( 'request', 32 )->default( '' );
			$table->text( 'message', 0x1ffff );

			$table->index( ['timestamp', 'siteid'], 'idx_malog_time_sid' );
			$table->index( ['facility', 'siteid'], 'idx_malog_facility_sid' );
			$table->index( ['priority', 'siteid'], 'idx_malog_prio_sid' );
		},
	),
);
