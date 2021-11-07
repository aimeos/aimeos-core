<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'code'], 'unq_mscus_sid_code' );
			$table->index( ['siteid', 'langid'], 'idx_mscus_sid_langid' );
			$table->index( ['siteid', 'lastname', 'firstname'], 'idx_mscus_sid_last_first' );
			$table->index( ['siteid', 'postal', 'address1'], 'idx_mscus_sid_post_addr1' );
			$table->index( ['siteid', 'postal', 'city'], 'idx_mscus_sid_post_city' );
			$table->index( ['siteid', 'city'], 'idx_mscus_sid_city' );
			$table->index( ['siteid', 'email'], 'idx_mscus_sid_email' );
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

			$table->index( ['parentid'], 'fk_mscusad_pid' );
			$table->index( ['langid'], 'idx_mscusad_langid' );
			$table->index( ['siteid', 'lastname', 'firstname'], 'idx_mscusad_sid_last_first' );
			$table->index( ['siteid', 'postal', 'address1'], 'idx_mscusad_sid_post_addr1' );
			$table->index( ['siteid', 'postal', 'city'], 'idx_mscusad_sid_post_ci' );
			$table->index( ['siteid', 'city'], 'idx_mscusad_sid_city' );
			$table->index( ['siteid', 'email'], 'idx_mscusad_sid_email' );

			$table->foreign( 'parentid', 'mshop_customer', 'id', 'fk_mscusad_pid' );
		},

		'mshop_customer_group' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscusgr_id' );
			$table->string( 'siteid' );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->meta();

			$table->unique( ['siteid', 'code'], 'unq_mscusgr_sid_code' );
			$table->index( ['siteid', 'label'], 'idx_mscusgr_sid_label' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mscuslity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mscuslity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mscuslity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mscuslity_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_mscusli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_mscusli_key_sid' );
			$table->index( ['parentid'], 'fk_mscusli_pid' );

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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mcusprty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mcusprty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mcusprty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mcusprty_sid_code' );
		},

		'mshop_customer_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mcuspr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 103 )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'siteid', 'type', 'langid', 'value'], 'unq_mcuspr_sid_ty_lid_value' );
			$table->index( ['key', 'siteid'], 'fk_mscuspr_key_sid' );
			$table->index( ['parentid'], 'fk_mcuspr_pid' );

			$table->foreign( 'parentid', 'mshop_customer', 'id', 'fk_mcuspr_pid' );
		},
	),
);
