<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_stock_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msstoty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msstoty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msstoty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msstoty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msstoty_sid_code' );
		},

		'mshop_stock' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mssto_id' );
			$table->string( 'siteid' );
			$table->refid( 'prodid' );
			$table->type();
			$table->int( 'stocklevel' )->null( true );
			$table->datetime( 'backdate' )->null( true );
			$table->string( 'timeframe', 16 )->default( '' );
			$table->meta();

			$table->unique( ['siteid', 'prodid', 'type'], 'unq_mssto_sid_pid_ty' );
			$table->index( ['siteid', 'stocklevel'], 'idx_mssto_sid_stocklevel' );
			$table->index( ['siteid', 'backdate'], 'idx_mssto_sid_backdate' );
		},
	),
);
