<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


return array(
	'table' => array(
		'mshop_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mstyp_id' );
			$table->string( 'siteid' );
			$table->string( 'for', 32 );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->i18n();
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['for', 'domain', 'code', 'siteid'], 'unq_mstyp_for_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mstyp_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mstyp_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mstyp_code_sid' );
		},
	),
);
