<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_media_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msmedty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msmedty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msmedty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msmedty_sid_code' );
		},

		'mshop_media' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmed_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'domain', 32 );
			$table->string( 'label' )->default( '' );
			$table->string( 'link' );
			$table->text( 'preview' )->default( '{}' );
			$table->string( 'mimetype', 64 )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['siteid', 'domain', 'langid'], 'idx_msmed_sid_dom_langid' );
			$table->index( ['siteid', 'domain', 'label'], 'idx_msmed_sid_dom_label' );
			$table->index( ['siteid', 'domain', 'mimetype'], 'idx_msmed_sid_dom_mime' );
			$table->index( ['siteid', 'domain', 'link'], 'idx_msmed_sid_dom_link' );
		},

		'mshop_media_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedlity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code( 'code' );
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msmedlity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msmedlity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msmedlity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msmedlity_sid_code' );
		},

		'mshop_media_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedli_id' );
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

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_msmedli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_msmedli_key_sid' );
			$table->index( ['parentid'], 'fk_msmedli_pid' );

			$table->foreign( 'parentid', 'mshop_media', 'id', 'fk_msmedli_pid' );
		},

		'mshop_media_property_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedprty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code( 'code' );
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msmedprty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msmedprty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msmedprty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msmedprty_sid_code' );
		},

		'mshop_media_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedpr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 103 )->default( '' );
			$table->type( 'type' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'siteid', 'type', 'langid', 'value'], 'unq_msmedpr_sid_ty_lid_value' );
			$table->index( ['key', 'siteid'], 'fk_msmedpr_key_sid' );
			$table->index( ['parentid'], 'fk_msmedpr_pid' );

			$table->foreign( 'parentid', 'mshop_media', 'id', 'fk_msmedpr_pid' );
		},
	),
);
