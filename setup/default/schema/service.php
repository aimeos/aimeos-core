<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_service_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msserty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msserty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msserty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msserty_code_sid' );
		},

		'mshop_service' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->code();
			$table->string( 'provider' );
			$table->string( 'label' )->default( '' );
			$table->startend();
			$table->config();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_msser_siteid_code_sid' );
			$table->index( ['status', 'start', 'end', 'siteid'], 'idx_msser_stat_start_end_sid' );
			$table->index( ['provider', 'siteid'], 'idx_msser_prov_sid' );
			$table->index( ['code', 'siteid'], 'idx_msser_code_sid' );
			$table->index( ['label', 'siteid'], 'idx_msser_label_sid' );
			$table->index( ['pos', 'siteid'], 'idx_msser_pos_sid' );
		},

		'mshop_service_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msserlity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msserlity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msserlity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msserlity_code_sid' );
		},

		'mshop_service_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 134 )->default( '' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->refid();
			$table->startend();
			$table->string( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_msserli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_msserli_key_sid' );

			$table->foreign( 'parentid', 'mshop_service', 'id', 'fk_msserli_pid' );
		},
	),
);
