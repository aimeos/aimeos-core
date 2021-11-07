<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->index( ['siteid', 'ctime'], 'idx_majob_sid_ctime' );
			$table->index( ['siteid', 'status'], 'idx_majob_sid_status' );
		},
	),
);
