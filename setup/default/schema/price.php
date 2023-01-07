<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_price_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msprity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msprity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msprity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msprity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msprity_code_sid' );
		},

		'mshop_price' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspri_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->string( 'label' )->default( '' );
			$table->string( 'currencyid', 3 );
			$table->float( 'quantity' )->default( 1 );
			$table->decimal( 'value', 12 )->null( true );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->string( 'taxrate' )->default( '{}' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['domain', 'currencyid', 'value', 'siteid'], 'idx_mspri_dom_cid_val_sid' );
		},

		'mshop_price_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msprility_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msprility_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msprility_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msprility_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msprility_code_sid' );
		},

		'mshop_price_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msprili_id' );
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

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_msprili_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_msprili_key_sid' );

			$table->foreign( 'parentid', 'mshop_price', 'id', 'fk_msprili_pid' );
		},

		'mshop_price_property_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspriprty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mspriprty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mspriprty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mspriprty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mspriprty_code_sid' );
		},

		'mshop_price_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspripr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key' )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'type', 'langid', 'value', 'siteid'], 'unq_mspripr_pid_ty_lid_val_sid' );
			$table->index( ['key', 'siteid'], 'idx_mspripr_key_sid' );

			$table->foreign( 'parentid', 'mshop_price', 'id', 'fk_mspripr_pid' );
		},
	),
);
