<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_product_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msproty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msproty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msproty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msproty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msproty_code_sid' );
		},

		'mshop_product' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspro_id' );
			$table->string( 'siteid' );
			$table->string( 'dataset', 64 )->default( '' );
			$table->type( 'type' );
			$table->code( 'code' );
			$table->string( 'label' )->default( '' );
			$table->string( 'url' )->default( '' );
			$table->config();
			$table->startend();
			$table->float( 'scale' )->default( 0 );
			$table->decimal( 'rating', 4 )->default( 0 );
			$table->int( 'ratings' )->default( 0 );
			$table->smallint( 'instock' )->default( 0 );
			$table->string( 'target' )->default( '' );
			$table->float( 'boost' )->default( 1 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mspro_code_sid' );
			$table->index( ['id', 'status', 'start', 'end', 'rating', 'siteid'], 'idx_mspro_id_stat_st_end_rt_sid' );
			$table->index( ['status', 'start', 'end', 'rating', 'siteid'], 'idx_mspro_stat_st_end_rt_sid' );
			$table->index( ['rating', 'siteid'], 'idx_mspro_rating_sid' );
			$table->index( ['label', 'siteid'], 'idx_mspro_label_sid' );
			$table->index( ['start', 'siteid'], 'idx_mspro_start_sid' );
			$table->index( ['type', 'siteid'], 'idx_mspro_type_sid' );
			$table->index( ['end', 'siteid'], 'idx_mspro_end_sid' );
		},

		'mshop_product_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msprolity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msprolity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msprolity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msprolity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msprolity_code_sid' );
		},

		'mshop_product_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msproli_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 134 )->default( '' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->refid();
			$table->startend();
			$table->config();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_msproli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_msproli_key_sid' );

			$table->foreign( 'parentid', 'mshop_product', 'id', 'fk_msproli_pid' );
		},

		'mshop_product_property_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msproprty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msproprty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msproprty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msproprty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msproprty_code_sid' );
		},

		'mshop_product_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspropr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key' )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'type', 'langid', 'value', 'siteid'], 'unq_mspropr_pid_ty_lid_val_sid' );
			$table->index( ['key', 'siteid'], 'idx_mspropr_key_sid' );

			$table->foreign( 'parentid', 'mshop_product', 'id', 'fk_mspropr_pid' );
		},
	),
);
