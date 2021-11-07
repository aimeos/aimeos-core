<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'nleft' );
			$table->int( 'nright' );
			$table->string( 'target' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'code'], 'unq_mscat_sid_code' );
			$table->index( ['siteid', 'nleft', 'nright', 'level', 'parentid'], 'idx_mscat_sid_nlt_nrt_lvl_pid' );
			$table->index( ['siteid', 'status'], 'idx_mscat_sid_status' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mscatlity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mscatlity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mscatlity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mscatlity_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_mscatli_pid_dm_sid_ty_rid' );
			$table->index( ['parentid', 'domain', 'siteid', 'pos', 'refid'], 'idx_mscatli_pid_dm_sid_pos_rid' );
			$table->index( ['refid', 'domain', 'siteid', 'type'], 'idx_mscatli_rid_dom_sid_ty' );
			$table->index( ['key', 'siteid'], 'idx_mscatli_key_sid' );
			$table->index( ['parentid'], 'fk_mscatli_pid' );

			$table->foreign( 'parentid', 'mshop_catalog', 'id', 'fk_mscatli_pid' );
		},
	),
);
