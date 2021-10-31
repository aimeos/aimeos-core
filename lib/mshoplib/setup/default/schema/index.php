<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_index_attribute' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->int( 'prodid' );
			$table->string( 'siteid' );
			$table->int( 'artid' )->null( true );
			$table->refid( 'attrid' );
			$table->type( 'listtype' );
			$table->type( 'type' );
			$table->code( 'code' );
			$table->datetime( 'mtime' );

			$table->unique( ['prodid', 'siteid', 'attrid', 'listtype'], 'unq_msindat_p_s_aid_lt' );
			$table->index( ['prodid', 'siteid', 'listtype', 'type', 'code'], 'idx_msindat_p_s_lt_t_c' );
			$table->index( ['siteid', 'attrid', 'listtype'], 'idx_msindat_s_at_lt' );
		},

		'mshop_index_catalog' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->int( 'prodid' );
			$table->string( 'siteid' );
			$table->refid( 'catid' );
			$table->type( 'listtype' );
			$table->int( 'pos' );
			$table->datetime( 'mtime' );

			$table->unique( ['prodid', 'siteid', 'catid', 'listtype', 'pos'], 'unq_msindca_p_s_cid_lt_po' );
			$table->index( ['siteid', 'catid', 'listtype', 'pos'], 'idx_msindca_s_ca_lt_po' );
		},

		'mshop_index_price' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->int( 'prodid' );
			$table->string( 'siteid' );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'value', 12 )->null( true );
			$table->datetime( 'mtime' );

			$table->unique( ['prodid', 'siteid', 'currencyid'], 'unq_msindpr_pid_sid_cid' );
			$table->index( ['siteid', 'currencyid', 'value'], 'idx_msindpr_sid_cid_val' );
		},

		'mshop_index_supplier' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->int( 'prodid' );
			$table->string( 'siteid' );
			$table->refid( 'supid' );
			$table->type( 'listtype' );
			$table->float( 'latitude' )->null( true );
			$table->float( 'longitude' )->null( true );
			$table->int( 'pos' );
			$table->datetime( 'mtime' );

			$table->unique( ['prodid', 'siteid', 'supid', 'listtype', 'pos'], 'unq_msindsup_p_sid_supid_lt_po' );
			$table->index( ['prodid', 'latitude', 'longitude', 'siteid'], 'idx_msindsup_p_lat_lon_sid' );
			$table->index( ['siteid', 'supid', 'listtype', 'pos'], 'idx_msindsup_sid_supid_lt_po' );
		},

		'mshop_index_text' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msindte_id' );
			$table->int( 'prodid' );
			$table->string( 'siteid' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'url' );
			$table->string( 'name' );
			$table->text( 'content', 0xffffff );
			$table->datetime( 'mtime' );

			$table->unique( ['prodid', 'siteid', 'langid', 'url'], 'unq_msindte_pid_sid_lid_url' );
			$table->index( ['prodid', 'siteid', 'langid', 'name'], 'idx_msindte_pid_sid_lid_name' );
		},
	),
);
