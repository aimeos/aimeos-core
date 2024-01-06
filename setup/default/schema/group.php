<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2024
 */


return array(
	'table' => array(
		'mshop_group' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msgro_id' );
			$table->string( 'siteid' );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_msgro_code_sid' );
			$table->index( ['label', 'siteid'], 'idx_msgro_label_sid' );
		},
	),
);
