<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(
		'mshop_order_basket' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'id' )->primary( 'pk_msordca_id' );
			$table->string( 'siteid' );
			$table->refid( 'customerid' )->default( '' );
			$table->text( 'content', 0x7fffff )->default( '' );
			$table->string( 'name' )->default( '' );
			$table->meta();

			$table->index( ['customerid'], 'idx_msordca_custid' );
			$table->index( ['mtime'], 'idx_msordca_mtime' );
		},

		'mshop_order' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msord_id' );
			$table->string( 'siteid' );
			$table->code( 'sitecode' )->length( 255 )->default( '' );
			$table->refid( 'customerid' )->default( '' );
			$table->refid( 'relatedid' )->default( '' );
			$table->string( 'channel', 16 )->default( '' );
			$table->string( 'invoiceno', 32 )->default( '' );
			$table->datetime( 'datepayment' )->null( true );
			$table->datetime( 'datedelivery' )->null( true );
			$table->smallint( 'statuspayment' )->default( -1 );
			$table->smallint( 'statusdelivery' )->default( -1 );
			$table->string( 'cdate', 10 )->default( '' );
			$table->string( 'cmonth', 7 )->default( '' );
			$table->string( 'cweek', 7 )->default( '' );
			$table->string( 'cwday', 1 )->default( '' );
			$table->string( 'chour', 2 )->default( '' );
			$table->string( 'langid', 5 )->default( '' );
			$table->string( 'currencyid', 3 )->default( '' );
			$table->decimal( 'price', 12 )->default( '0.00' );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.0000' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->string( 'customerref' )->default( '' );
			$table->text( 'comment' )->default( '' );
			$table->meta();

			$table->index( ['channel', 'siteid'], 'idx_msord_channel_sid' );
			$table->index( ['customerid', 'siteid'], 'idx_msord_custid_sid' );
			$table->index( ['customerid', 'sitecode'], 'idx_msord_custid_scode' );
			$table->index( ['ctime', 'statuspayment', 'siteid'], 'idx_msord_ctime_pstat_sid' );
			$table->index( ['mtime', 'statuspayment', 'siteid'], 'idx_msord_mtime_pstat_sid' );
			$table->index( ['mtime', 'statusdelivery', 'siteid'], 'idx_msord_mtime_dstat_sid' );
			$table->index( ['statusdelivery', 'siteid'], 'idx_msord_dstat_sid' );
			$table->index( ['datedelivery', 'siteid'], 'idx_msord_ddate_sid' );
			$table->index( ['datepayment', 'siteid'], 'idx_msord_pdate_sid' );
			$table->index( ['editor', 'siteid'], 'idx_msord_editor_sid' );
			$table->index( ['cdate', 'siteid'], 'idx_msord_cdate_sid' );
			$table->index( ['cmonth', 'siteid'], 'idx_msord_cmonth_sid' );
			$table->index( ['cweek', 'siteid'], 'idx_msord_cweek_sid' );
			$table->index( ['cwday', 'siteid'], 'idx_msord_cwday_sid' );
			$table->index( ['chour', 'siteid'], 'idx_msord_chour_sid' );
		},

		'mshop_order_address' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordad_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->refid( 'addrid' )->default( '' );
			$table->type();
			$table->string( 'salutation', 8 )->default( '' );
			$table->string( 'company', 100 )->default( '' );
			$table->string( 'vatid', 32 )->default( '' );
			$table->string( 'title', 64 )->default( '' );
			$table->string( 'firstname', 64 )->default( '' );
			$table->string( 'lastname', 64 )->default( '' );
			$table->string( 'address1', 200 )->default( '' );
			$table->string( 'address2', 200 )->default( '' );
			$table->string( 'address3', 200 )->default( '' );
			$table->string( 'postal', 16 )->default( '' );
			$table->string( 'city', 200 )->default( '' );
			$table->string( 'state', 200 )->default( '' );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'countryid', 2 )->null( true );
			$table->string( 'telephone', 32 )->default( '' );
			$table->string( 'telefax', 32 )->default( '' );
			$table->string( 'email' )->default( '' );
			$table->string( 'website' )->default( '' );
			$table->float( 'longitude' )->null( true );
			$table->float( 'latitude' )->null( true );
			$table->date( 'birthday' )->null( true );
			$table->int( 'pos' )->default( 0 );
			$table->meta();

			$table->unique( ['parentid', 'type'], 'unq_msordad_pid_type' );
			$table->index( ['parentid', 'lastname'], 'idx_msordad_pid_lname' );
			$table->index( ['parentid', 'address1'], 'idx_msordad_pid_addr1' );
			$table->index( ['parentid', 'postal'], 'idx_msordad_pid_postal' );
			$table->index( ['parentid', 'city'], 'idx_msordad_pid_city' );
			$table->index( ['parentid', 'email'], 'idx_msordad_pid_email' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordad_parentid' );
		},

		'mshop_order_product' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordpr_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->bigint( 'ordprodid' )->null( true );
			$table->bigint( 'ordaddrid' )->null( true );
			$table->type();
			$table->refid( 'prodid' )->default( '' );
			$table->refid( 'parentprodid' )->default( '' );
			$table->code( 'prodcode' );
			$table->type( 'stocktype' )->default( 'default' );
			$table->string( 'vendor' )->default( '' );
			$table->string( 'name' )->default( '' );
			$table->text( 'description' )->default( '' );
			$table->string( 'mediaurl' )->default( '' );
			$table->string( 'target' )->default( '' );
			$table->string( 'timeframe', 16 )->default( '' );
			$table->float( 'quantity' )->default( 1 );
			$table->float( 'qtyopen' )->default( 0 );
			$table->float( 'scale' )->default( 1 );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->null( true );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.0000' );
			$table->string( 'taxrate' )->default( '{}' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->int( 'flags' )->default( 0 );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'statuspayment' )->default( -1 );
			$table->smallint( 'statusdelivery' )->default( -1 );
			$table->text( 'notes' )->default( '' );
			$table->meta();

			$table->unique( ['parentid', 'pos'], 'unq_msordpr_pid_pos' );
			$table->index( ['parentid', 'prodid'], 'idx_msordpr_pid_prid' );
			$table->index( ['parentid', 'prodcode'], 'idx_msordpr_pid_pcd' );
			$table->index( ['parentid', 'qtyopen'], 'idx_msordpr_pid_qtyo' );
			$table->index( ['ctime', 'prodid', 'parentid'], 'idx_msordpr_ct_prid_pid' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordpr_parentid' );
		},

		'mshop_order_product_attr' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordprat_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->refid( 'attrid' )->default( '' );
			$table->type();
			$table->code()->length( 255 );
			$table->float( 'quantity' )->default( 1 );
			$table->decimal( 'price', 12 )->null( true );
			$table->string( 'name' )->default( '' );
			$table->text( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'attrid', 'type', 'code'], 'unq_msordprat_pid_aid_ty_cd' );

			$table->foreign( 'parentid', 'mshop_order_product', 'id', 'fk_msordprat_parentid' );
		},

		'mshop_order_service' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordse_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->refid( 'servid' )->default( '' );
			$table->type();
			$table->code();
			$table->string( 'name' )->default( '' );
			$table->string( 'mediaurl' )->default( '' );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->null( true );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.0000' );
			$table->string( 'taxrate' )->default( '{}' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->int( 'pos' )->default( 0 );
			$table->meta();

			$table->unique( ['parentid', 'code', 'type', 'siteid'], 'unq_msordse_pid_cd_typ_sid' );
			$table->index( ['code', 'type', 'siteid'], 'idx_msordse_code_type_sid' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordse_parentid' );
		},

		'mshop_order_service_attr' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordseat_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->refid( 'attrid' )->default( '' );
			$table->type();
			$table->code()->length( 255 );
			$table->float( 'quantity' )->default( 1 );
			$table->decimal( 'price', 12 )->null( true );
			$table->string( 'name' )->default( '' );
			$table->text( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'attrid', 'type', 'code'], 'unq_msordseat_pid_aid_ty_cd' );

			$table->foreign( 'parentid', 'mshop_order_service', 'id', 'fk_msordseat_parentid' );
		},

		'mshop_order_service_tx' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordsetx_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->string( 'type', 16 )->default( '' );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->default( '0.00' );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.0000' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->smallint( 'status' )->default( -1 );
			$table->config();
			$table->meta();

			$table->foreign( 'parentid', 'mshop_order_service', 'id', 'fk_msordsetx_parentid' );
		},

		'mshop_order_coupon' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordco_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->bigint( 'ordprodid' )->null( true );
			$table->code();
			$table->meta();

			$table->index( ['parentid', 'code'], 'idx_msordco_pid_code' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordco_parentid' );
		},

		'mshop_order_status' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordst_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->type();
			$table->string( 'value', 64 );
			$table->meta();

			$table->index( ['parentid', 'type', 'value', 'siteid'], 'idx_msordst_pid_typ_val_sid' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordst_pid' );
		},
	),
);
