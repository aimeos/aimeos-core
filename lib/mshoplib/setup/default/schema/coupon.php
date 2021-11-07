<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(

		'mshop_coupon' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscou_id' );
			$table->string( 'siteid' );
			$table->string( 'label' )->default( '' );
			$table->string( 'provider' );
			$table->text( 'config' )->default( '{}' );
			$table->startend();
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['siteid', 'status', 'start', 'end'], 'idx_mscou_sid_stat_start_end' );
			$table->index( ['siteid', 'provider'], 'idx_mscou_sid_provider' );
			$table->index( ['siteid', 'label'], 'idx_mscou_sid_label' );
			$table->index( ['siteid', 'start'], 'idx_mscou_sid_start' );
			$table->index( ['siteid', 'end'], 'idx_mscou_sid_end' );
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

			$table->unique( ['siteid', 'code'], 'unq_mscouco_sid_code' );
			$table->index( ['siteid', 'count', 'start', 'end'], 'idx_mscouco_sid_ct_start_end' );
			$table->index( ['siteid', 'start'], 'idx_mscouco_sid_start' );
			$table->index( ['siteid', 'end'], 'idx_mscouco_sid_end' );
			$table->index( ['parentid'], 'fk_mscouco_pid' );

			$table->foreign( 'parentid', 'mshop_coupon', 'id', 'fk_mscouco_pid' );
		},
	)
);
