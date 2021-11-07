<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msrulty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msrulty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msrulty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msrulty_sid_code' );
		},

		'mshop_rule' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msrul_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'provider' );
			$table->string( 'label' )->default( '' );
			$table->text( 'config' )->default( '{}' );
			$table->startend();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['siteid', 'provider'], 'idx_msrul_sid_prov' );
			$table->index( ['siteid', 'status'], 'idx_msrul_sid_status' );
			$table->index( ['siteid', 'label'], 'idx_msrul_sid_label' );
			$table->index( ['siteid', 'pos'], 'idx_msrul_sid_pos' );
			$table->index( ['siteid', 'start'], 'idx_msrul_sid_start' );
			$table->index( ['siteid', 'end'], 'idx_msrul_sid_end' );
		},
	),
);
