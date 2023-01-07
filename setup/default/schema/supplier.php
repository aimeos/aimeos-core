<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(

		'mshop_supplier' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mssup_id' );
			$table->string( 'siteid' );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->int( 'pos' )->default( 0 );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mssup_code_sid' );
			$table->index( ['label', 'siteid'], 'idx_mssup_label_sid' );
			$table->index( ['siteid', 'status', 'pos', 'label'], 'idx_mssup_sid_stat_pos_label' );
		},

		'mshop_supplier_address' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mssupad_id' );
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

			$table->foreign( 'parentid', 'mshop_supplier', 'id', 'fk_mssupad_pid' );
		},

		'mshop_supplier_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mssuplity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mssuplity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mssuplity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mssuplity_sid_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mssuplity_sid_code_sid' );
		},

		'mshop_supplier_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mssupli_id' );
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

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_mssupli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_mssupli_key_sid' );

			$table->foreign( 'parentid', 'mshop_supplier', 'id', 'fk_mssupli_pid' );
		},
	),
);
