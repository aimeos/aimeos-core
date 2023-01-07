<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_attribute_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msattty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msattty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msattty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msattty_code_sid' );
		},

		'mshop_attribute' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->string( 'key' )->default( '' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->code()->length( 255 );
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'type', 'code', 'siteid'], 'unq_msatt_dom_type_code_sid' );
			$table->index( ['domain', 'siteid', 'status', 'type', 'pos'], 'idx_msatt_dom_sid_stat_typ_pos' );
			$table->index( ['status', 'siteid'], 'idx_msatt_status_sid' );
			$table->index( ['label', 'siteid'], 'idx_msatt_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msatt_code_sid' );
			$table->index( ['type', 'siteid'], 'idx_msatt_type_sid' );
			$table->index( ['key', 'siteid'], 'idx_msatt_key_sid' );
		},

		'mshop_attribute_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattlity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msattlity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msattlity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msattlity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msattlity_code_sid' );
		},

		'mshop_attribute_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattli_id' );
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

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_msattli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_msattli_key_sid' );

			$table->foreign( 'parentid', 'mshop_attribute', 'id', 'fk_msattli_pid' );
		},

		'mshop_attribute_property_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattprty_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_msattprty_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_msattprty_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_msattprty_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_msattprty_code_sid' );
		},

		'mshop_attribute_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattpr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key' )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'type', 'langid', 'value', 'siteid'], 'unq_msattpr_ty_lid_value_sid' );
			$table->index( ['key', 'siteid'], 'idx_msattpr_key_sid' );

			$table->foreign( 'parentid', 'mshop_attribute', 'id', 'fk_msattpr_pid' );
		},
	),
);
