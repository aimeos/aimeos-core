<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_plugin_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspluty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mspluty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mspluty_status_pos_sid' );
			$table->index( ['label', 'siteid'], 'idx_mspluty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mspluty_code_sid' );
		},

		'mshop_plugin' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msplu_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'provider' );
			$table->string( 'label' )->default( '' );
			$table->config();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['type', 'provider', 'siteid'], 'unq_msplu_ty_prov_sid' );
			$table->index( ['provider', 'siteid'], 'idx_msplu_prov_sid' );
			$table->index( ['status', 'siteid'], 'idx_msplu_status_sid' );
			$table->index( ['label', 'siteid'], 'idx_msplu_label_sid' );
			$table->index( ['pos', 'siteid'], 'idx_msplu_pos_sid' );
		},
	),
);
