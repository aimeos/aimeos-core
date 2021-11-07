<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msattty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msattty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msattty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msattty_sid_code' );
		},

		'mshop_attribute' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msatt_id' );
			$table->string( 'siteid' );
			$table->string( 'key', 32 )->default( '' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->code()->length( 255 );
			$table->string( 'label' )->default( '' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['domain', 'siteid', 'type', 'code'], 'unq_msatt_dom_sid_type_code' );
			$table->index( ['domain', 'siteid', 'status', 'type', 'pos'], 'idx_msatt_dom_sid_stat_typ_pos' );
			$table->index( ['siteid', 'status'], 'idx_msatt_sid_status' );
			$table->index( ['siteid', 'label'], 'idx_msatt_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msatt_sid_code' );
			$table->index( ['siteid', 'type'], 'idx_msatt_sid_type' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msattlity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msattlity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msattlity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msattlity_sid_code' );
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
			$table->text( 'config' )->default( '{}' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_msattli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_msattli_key_sid' );
			$table->index( ['parentid'], 'fk_msattli_pid' );

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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_msattprty_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_msattprty_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_msattprty_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_msattprty_sid_code' );
		},

		'mshop_attribute_property' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msattpr_id' );
			$table->string( 'siteid' );
			$table->int( 'parentid' );
			$table->string( 'key', 103 )->default( '' );
			$table->type();
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'siteid', 'type', 'langid', 'value'], 'unq_msattpr_sid_ty_lid_value' );
			$table->index( ['key', 'siteid'], 'fk_msattpr_key_sid' );
			$table->index( ['parentid'], 'fk_msattpr_pid' );

			$table->foreign( 'parentid', 'mshop_attribute', 'id', 'fk_msattpr_pid' );
		},
	),
);
