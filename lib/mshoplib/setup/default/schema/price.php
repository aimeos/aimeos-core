<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msprity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msprity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msprity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msprity_sid_code' );
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

			$table->index( ['siteid', 'domain', 'currencyid'], 'idx_mspri_sid_dom_currid' );
			$table->index( ['siteid', 'domain', 'quantity'], 'idx_mspri_sid_dom_quantity' );
			$table->index( ['siteid', 'domain', 'value'], 'idx_mspri_sid_dom_value' );
			$table->index( ['siteid', 'domain', 'costs'], 'idx_mspri_sid_dom_costs' );
			$table->index( ['siteid', 'domain', 'rebate'], 'idx_mspri_sid_dom_rebate' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msprility_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msprility_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msprility_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msprility_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_msprili_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_msprili_key_sid' );
			$table->index( ['parentid'], 'fk_msprili_pid' );

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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mspriprty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mspriprty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mspriprty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mspriprty_sid_code' );
		},

		'mshop_price_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mspripr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 103 )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'siteid', 'type', 'langid', 'value'], 'unq_mspripr_sid_ty_lid_value' );
			$table->index( ['key', 'siteid'], 'fk_mspripr_key_sid' );
			$table->index( ['parentid'], 'fk_mspripr_pid' );

			$table->foreign( 'parentid', 'mshop_price', 'id', 'fk_mspripr_pid' );
		},
	),
);
