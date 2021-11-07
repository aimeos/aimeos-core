<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msserty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msserty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msserty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msserty_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'code'], 'unq_msser_siteid_code' );
			$table->index( ['siteid', 'status', 'start', 'end'], 'idx_msser_sid_stat_start_end' );
			$table->index( ['siteid', 'provider'], 'idx_msser_sid_prov' );
			$table->index( ['siteid', 'code'], 'idx_msser_sid_code' );
			$table->index( ['siteid', 'label'], 'idx_msser_sid_label' );
			$table->index( ['siteid', 'pos'], 'idx_msser_sid_pos' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msserlity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msserlity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msserlity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msserlity_sid_code' );
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

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_msserli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_msserli_key_sid' );
			$table->index( ['parentid'], 'fk_msserli_pid' );

			$table->foreign( 'parentid', 'mshop_service', 'id', 'fk_msserli_pid' );
		},
	),
);
