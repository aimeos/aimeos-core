<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
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

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msstoty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msstoty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msstoty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msstoty_code_sid' );
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

			$table->unique( ['prodid', 'type', 'siteid'], 'unq_mssto_pid_ty_sid' );
			$table->index( ['stocklevel', 'siteid'], 'idx_mssto_stocklevel_sid' );
			$table->index( ['backdate', 'siteid'], 'idx_mssto_backdate_sid' );
		},
	),
);
