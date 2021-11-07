<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mspluty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mspluty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mspluty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mspluty_sid_code' );
		},

		'mshop_plugin' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msplu_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'provider' );
			$table->string( 'label' )->default( '' );
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'type', 'provider'], 'unq_msplu_sid_ty_prov' );
			$table->index( ['siteid', 'provider'], 'idx_msplu_sid_prov' );
			$table->index( ['siteid', 'status'], 'idx_msplu_sid_status' );
			$table->index( ['siteid', 'label'], 'idx_msplu_sid_label' );
			$table->index( ['siteid', 'pos'], 'idx_msplu_sid_pos' );
		},
	),
);
