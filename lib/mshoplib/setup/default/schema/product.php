<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msproty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msproty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msproty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msproty_sid_code' );
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
			$table->text( 'config' )->default( '' );
			$table->startend();
			$table->float( 'scale' )->default( 0 );
			$table->decimal( 'rating', 4 )->default( 0 );
			$table->int( 'ratings' )->default( 0 );
			$table->smallint( 'instock' )->default( 0 );
			$table->string( 'target' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'code'], 'unq_mspro_siteid_code' );
			$table->index( ['id', 'siteid', 'status', 'start', 'end', 'rating'], 'idx_mspro_id_sid_stat_st_end_rt' );
			$table->index( ['siteid', 'status', 'start', 'end', 'rating'], 'idx_mspro_sid_stat_st_end_rt' );
			$table->index( ['siteid', 'rating'], 'idx_mspro_sid_rating' );
			$table->index( ['siteid', 'label'], 'idx_mspro_sid_label' );
			$table->index( ['siteid', 'start'], 'idx_mspro_sid_start' );
			$table->index( ['siteid', 'end'], 'idx_mspro_sid_end' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msprolity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msprolity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msprolity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msprolity_sid_code' );
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
			$table->text( 'config' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_msproli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_msproli_key_sid' );
			$table->index( ['parentid'], 'fk_msproli_pid' );

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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msproprty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msproprty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msproprty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msproprty_sid_code' );
		},

		'mshop_product_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspropr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 103 )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'siteid', 'type', 'langid', 'value'], 'unq_mspropr_sid_ty_lid_value' );
			$table->index( ['key', 'siteid'], 'fk_mspropr_key_sid' );
			$table->index( ['parentid'], 'fk_mspropr_pid' );

			$table->foreign( 'parentid', 'mshop_product', 'id', 'fk_mspropr_pid' );
		},
	),
);
