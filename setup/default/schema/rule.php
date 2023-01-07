<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


return array(
	'table' => array(
		'mshop_rule_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msrulty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msrulty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msrulty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msrulty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msrulty_code_sid' );
		},

		'mshop_rule' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msrul_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'provider' );
			$table->string( 'label' )->default( '' );
			$table->config();
			$table->startend();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['provider', 'siteid'], 'idx_msrul_prov_sid' );
			$table->index( ['status', 'siteid'], 'idx_msrul_status_sid' );
			$table->index( ['label', 'siteid'], 'idx_msrul_label_sid' );
			$table->index( ['pos', 'siteid'], 'idx_msrul_pos_sid' );
			$table->index( ['start', 'siteid'], 'idx_msrul_start_sid' );
			$table->index( ['end', 'siteid'], 'idx_msrul_end_sid' );
		},
	),
);
