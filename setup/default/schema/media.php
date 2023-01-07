<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
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

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msmedty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msmedty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msmedty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msmedty_code_sid' );
		},

		'mshop_media' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmed_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'fsname', 32 )->default( '' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'domain', 32 );
			$table->string( 'label' )->default( '' );
			$table->string( 'link' );
			$table->text( 'preview' )->default( '{}' );
			$table->string( 'mimetype', 64 )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['domain', 'mimetype', 'siteid'], 'idx_msmed_dom_mime_sid' );
			$table->index( ['label', 'siteid'], 'idx_msmed_label_sid' );
			$table->index( ['link', 'siteid'], 'idx_msmed_link_sid' );
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

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msmedlity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msmedlity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msmedlity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msmedlity_code_sid' );
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
			$table->config();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_msmedli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_msmedli_key_sid' );

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

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msmedprty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msmedprty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msmedprty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msmedprty_code_sid' );
		},

		'mshop_media_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msmedpr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key' )->default( '' );
			$table->type( 'type' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'type', 'langid', 'value', 'siteid'], 'unq_msmedpr_pid_ty_lid_val_sid' );
			$table->index( ['key', 'siteid'], 'idx_msmedpr_key_sid' );

			$table->foreign( 'parentid', 'mshop_media', 'id', 'fk_msmedpr_pid' );
		},
	),
);
