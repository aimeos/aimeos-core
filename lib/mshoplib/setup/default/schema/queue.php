<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'madmin_queue' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_maque_id' );
			$table->string( 'queue' );
			$table->string( 'cname', 32 );
			$table->datetime( 'rtime' );
			$table->text( 'message' );

			$table->index( ['queue', 'rtime', 'cname'], 'idx_maque_queue_rtime_cname' );
		},
	),
);
