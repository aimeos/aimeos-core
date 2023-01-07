<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(

	'table' => array(
		'mshop_text_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstexty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mstexty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mstexty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mstexty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mstexty_code_sid' );
		},

		'mshop_text' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstex_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'domain', 32 );
			$table->string( 'label' )->default( '' );
			$table->text( 'content', 0xffffff );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['domain', 'status', 'siteid'], 'idx_mstex_dom_stat_sid' );
			$table->index( ['langid', 'siteid'], 'idx_mstex_langid_sid' );
			$table->index( ['label', 'siteid'], 'idx_mstex_label_sid' );
		},

		'mshop_text_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstexlity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mstexlity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mstexlity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mstexlity_label' );
			$table->index( ['code', 'siteid'], 'idx_mstexlity_code' );
		},

		'mshop_text_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstexli_id' );
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

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_mstexli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_mstexli_key_sid' );

			$table->foreign( 'parentid', 'mshop_text', 'id', 'fk_mstexli_pid' );
		},
	),
);
