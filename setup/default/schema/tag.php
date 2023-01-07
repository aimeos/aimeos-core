<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_tag_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstagty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mstagty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mstagty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mstagty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mstagty_code_sid' );
		},

		'mshop_tag' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstag_id' );
			$table->string( 'siteid' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'domain', 32 );
			$table->string( 'label' );
			$table->meta();

			$table->unique( ['domain', 'type', 'langid', 'label', 'siteid'], 'unq_mstag_dom_ty_lid_lab_sid' );
			$table->index( ['langid', 'domain', 'siteid'], 'idx_mstag_dom_langid_sid' );
			$table->index( ['label', 'domain', 'siteid'], 'idx_mstag_dom_label_sid' );
		},
	),
);
