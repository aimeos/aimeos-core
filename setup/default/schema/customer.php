<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_customer' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscus_id' );
			$table->string( 'siteid' );
			$table->string( 'code' );
			$table->string( 'label' )->default( '' );
			$table->string( 'salutation', 8 )->default( '' );
			$table->string( 'company', 100 )->default( '' );
			$table->string( 'vatid', 32 )->default( '' );
			$table->string( 'title', 64 )->default( '' );
			$table->string( 'firstname', 64 )->default( '' );
			$table->string( 'lastname', 64 )->default( '' );
			$table->string( 'address1', 200 )->default( '' );
			$table->string( 'address2', 200 )->default( '' );
			$table->string( 'address3', 200 )->default( '' );
			$table->string( 'postal', 16 )->default( '' );
			$table->string( 'city', 200 )->default( '' );
			$table->string( 'state', 200 )->default( '' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'countryid', 2 )->null( true );
			$table->string( 'telephone', 32 )->default( '' );
			$table->string( 'telefax', 32 )->default( '' );
			$table->string( 'email' )->default( '' );
			$table->string( 'website' )->default( '' );
			$table->float( 'longitude' )->null( true );
			$table->float( 'latitude' )->null( true );
			$table->date( 'birthday' )->null( true );
			$table->date( 'vdate' )->null( true );
			$table->string( 'password' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mscus_code_sid' );
			$table->index( ['langid', 'siteid'], 'idx_mscus_langid_sid' );
			$table->index( ['lastname', 'firstname'], 'idx_mscus_last_first' );
			$table->index( ['postal', 'address1'], 'idx_mscus_post_addr1' );
			$table->index( ['postal', 'city'], 'idx_mscus_post_city' );
			$table->index( ['city'], 'idx_mscus_city' );
			$table->index( ['email'], 'idx_mscus_email' );
		},

		'mshop_customer_address' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscusad_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'company', 100 )->default( '' );
			$table->string( 'vatid', 32 )->default( '' );
			$table->string( 'salutation', 8 )->default( '' );
			$table->string( 'title', 64 )->default( '' );
			$table->string( 'firstname', 64 )->default( '' );
			$table->string( 'lastname', 64 )->default( '' );
			$table->string( 'address1', 200 )->default( '' );
			$table->string( 'address2', 200 )->default( '' );
			$table->string( 'address3', 200 )->default( '' );
			$table->string( 'postal', 16 )->default( '' );
			$table->string( 'city', 200 )->default( '' );
			$table->string( 'state', 200 )->default( '' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'countryid', 2 )->null( true );
			$table->string( 'telephone', 32 )->default( '' );
			$table->string( 'telefax', 32 )->default( '' );
			$table->string( 'email' )->default( '' );
			$table->string( 'website' )->default( '' );
			$table->float( 'longitude' )->null( true );
			$table->float( 'latitude' )->null( true );
			$table->date( 'birthday' )->null( true );
			$table->smallint( 'pos' )->default( 0 );
			$table->meta();

			$table->index( ['langid', 'siteid'], 'idx_mscusad_langid_sid' );
			$table->index( ['lastname', 'firstname'], 'idx_mscusad_last_first' );
			$table->index( ['postal', 'address1'], 'idx_mscusad_post_addr1' );
			$table->index( ['postal', 'city'], 'idx_mscusad_post_city' );
			$table->index( ['city'], 'idx_mscusad_city' );
			$table->index( ['email'], 'idx_mscusad_email' );

			$table->foreign( 'parentid', 'mshop_customer', 'id', 'fk_mscusad_pid' );
		},

		'mshop_customer_group' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscusgr_id' );
			$table->string( 'siteid' );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mscusgr_code_sid' );
			$table->index( ['label', 'siteid'], 'idx_mscusgr_label_sid' );
		},

		'mshop_customer_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscuslity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mscuslity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mscuslity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mscuslity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mscuslity_code_sid' );
		},

		'mshop_customer_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscusli_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 134 )->default( '' );
			$table->type( 'type' );
			$table->string( 'domain', 32 );
			$table->refid();
			$table->startend();
			$table->config();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_mscusli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_mscusli_key_sid' );

			$table->foreign( 'parentid', 'mshop_customer', 'id', 'fk_mscusli_pid' );
		},

		'mshop_customer_property_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mcusprty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mscusprty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mscusprty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mscusprty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mscusprty_code_sid' );
		},

		'mshop_customer_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mcuspr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key' )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'type', 'langid', 'value', 'siteid'], 'unq_mscuspr_pid_ty_lid_val_sid' );
			$table->index( ['key', 'siteid'], 'idx_mscuspr_key_sid' );

			$table->foreign( 'parentid', 'mshop_customer', 'id', 'fk_mcuspr_pid' );
		},
	),
);
