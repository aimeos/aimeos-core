<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'madmin_job' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_majob_id' );
			$table->string( 'siteid' );
			$table->string( 'path' );
			$table->string( 'label' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['ctime', 'siteid'], 'idx_majob_ctime_sid' );
			$table->index( ['status', 'siteid'], 'idx_majob_status_sid' );
		},
	),
);
