<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(

	'exclude' => array(
		'idx_mstex_sid_dom_cont',
	),


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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mstexty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mstexty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mstexty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mstexty_sid_code' );
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

			$table->index( ['siteid', 'domain', 'status'], 'idx_mstex_sid_domain_status' );
			$table->index( ['siteid', 'domain', 'langid'], 'idx_mstex_sid_domain_langid' );
			$table->index( ['siteid', 'domain', 'label'], 'idx_mstex_sid_dom_label' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mstexlity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mstexlity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mstexlity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mstexlity_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_mstexli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_mstexli_key_sid' );
			$table->index( ['parentid'], 'fk_mstexli_pid' );

			$table->foreign( 'parentid', 'mshop_text', 'id', 'fk_mstexli_pid' );
		},
	),
);
