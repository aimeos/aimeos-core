<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(

		'mshop_coupon' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscou_id' );
			$table->string( 'siteid' );
			$table->string( 'label' )->default( '' );
			$table->string( 'provider' );
			$table->config();
			$table->startend();
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['status', 'start', 'end', 'siteid'], 'idx_mscou_stat_start_end_sid' );
			$table->index( ['provider', 'siteid'], 'idx_mscou_provider_sid' );
			$table->index( ['label', 'siteid'], 'idx_mscou_label_sid' );
			$table->index( ['start', 'siteid'], 'idx_mscou_start_sid' );
			$table->index( ['end', 'siteid'], 'idx_mscou_end_sid' );
		},

		'mshop_coupon_code' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscouco_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->code();
			$table->int( 'count' )->null( true )->default( 0 );
			$table->startend();
			$table->refid( 'ref' )->default( '' );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mscouco_code_sid' );
			$table->index( ['count', 'start', 'end', 'siteid'], 'idx_mscouco_ct_start_end_sid' );
			$table->index( ['start', 'siteid'], 'idx_mscouco_start_sid' );
			$table->index( ['end', 'siteid'], 'idx_mscouco_end_sid' );

			$table->foreign( 'parentid', 'mshop_coupon', 'id', 'fk_mscouco_pid' );
		},
	)
);
