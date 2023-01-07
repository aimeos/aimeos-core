<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(

		'mshop_catalog' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscat_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' )->null( true );
			$table->smallint( 'level' )->default( 0 );
			$table->code( 'code' );
			$table->string( 'label' )->default( '' );
			$table->string( 'url' )->default( '' );
			$table->config();
			$table->int( 'nleft' );
			$table->int( 'nright' );
			$table->string( 'target' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['code', 'siteid'], 'unq_mscat_code_sid' );
			$table->index( ['nleft', 'nright', 'level', 'parentid', 'siteid'], 'idx_mscat_nlt_nrt_lvl_pid_sid' );
			$table->index( ['status', 'siteid'], 'idx_mscat_status_sid' );
		},

		'mshop_catalog_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscatlity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mscatlity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mscatlity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mscatlity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mscatlity_code_sid' );
		},

		'mshop_catalog_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mscatli_id' );
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

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_mscatli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_mscatli_key_sid' );

			$table->foreign( 'parentid', 'mshop_catalog', 'id', 'fk_mscatli_pid' );
		},
	),
);
