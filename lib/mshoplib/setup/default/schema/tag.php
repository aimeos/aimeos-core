<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mstagty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mstagty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mstagty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mstagty_sid_code' );
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

			$table->unique( ['siteid', 'domain', 'type', 'langid', 'label'], 'unq_mstag_sid_dom_ty_lid_lab' );
			$table->index( ['siteid', 'domain', 'langid'], 'idx_mstag_sid_dom_langid' );
			$table->index( ['siteid', 'domain', 'label'], 'idx_mstag_sid_dom_label' );
		},
	),
);
