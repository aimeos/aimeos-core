<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_order_base' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordba_id' );
			$table->string( 'siteid' );
			$table->refid( 'customerid' );
			$table->code( 'sitecode' )->length( 255 )->null( true );
			$table->string( 'langid', 5 );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 );
			$table->decimal( 'costs', 12 );
			$table->decimal( 'rebate', 12 );
			$table->decimal( 'tax', 14, 4 );
			$table->smallint( 'taxflag' );
			$table->string( 'customerref' )->null( true );
			$table->text( 'comment' );
			$table->meta();

			$table->index( ['customerid', 'sitecode'], 'idx_msordba_custid_scode' );
			$table->index( ['customerid', 'siteid'], 'idx_msordba_custid_sid' );
			$table->index( ['siteid', 'ctime'], 'idx_msordba_sid_ctime' );
		},

		'mshop_order_base_address' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbaad_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->refid( 'addrid' );
			$table->type();
			$table->string( 'salutation', 8 );
			$table->string( 'company', 100 );
			$table->string( 'vatid', 32 );
			$table->string( 'title', 64 );
			$table->string( 'firstname', 64 );
			$table->string( 'lastname', 64 );
			$table->string( 'address1', 200 );
			$table->string( 'address2', 200 );
			$table->string( 'address3', 200 );
			$table->string( 'postal', 16 );
			$table->string( 'city', 200 );
			$table->string( 'state', 200 );
			$table->string( 'langid', 5 )->null( true );
			$table->string( 'countryid', 2 )->null( true );
			$table->string( 'telephone', 32 );
			$table->string( 'telefax', 32 );
			$table->string( 'email' );
			$table->string( 'website' );
			$table->float( 'longitude' )->null( true );
			$table->float( 'latitude' )->null( true );
			$table->date( 'birthday' )->null( true );
			$table->int( 'pos' );
			$table->meta();

			$table->unique( ['baseid', 'type'], 'unq_msordbaad_bid_type' );
			$table->index( ['siteid', 'baseid', 'type'], 'idx_msordbaad_sid_bid_typ' );
			$table->index( ['baseid', 'siteid', 'lastname'], 'idx_msordbaad_bid_sid_lname' );
			$table->index( ['baseid', 'siteid', 'address1'], 'idx_msordbaad_bid_sid_addr1' );
			$table->index( ['baseid', 'siteid', 'postal'], 'idx_msordbaad_bid_sid_postal' );
			$table->index( ['baseid', 'siteid', 'city'], 'idx_msordbaad_bid_sid_city' );
			$table->index( ['baseid', 'siteid', 'email'], 'idx_msordbaad_bid_sid_email' );
			$table->index( ['baseid'], 'fk_msordbaad_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msordbaad_baseid' );
		},

		'mshop_order_base_product' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbapr_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->bigint( 'ordprodid' )->null( true );
			$table->bigint( 'ordaddrid' )->null( true );
			$table->type();
			$table->refid( 'prodid' );
			$table->refid( 'parentprodid' )->default( '' );
			$table->code( 'prodcode' );
			$table->type( 'stocktype' );
			$table->string( 'suppliername' )->default( '' );
			$table->refid( 'supplierid' )->default( '' );
			$table->text( 'name' );
			$table->text( 'description' )->default( '' );
			$table->string( 'mediaurl' );
			$table->string( 'target' );
			$table->string( 'timeframe', 16 )->default( '' );
			$table->float( 'quantity' );
			$table->float( 'qtyopen' )->default( 0 );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->null( true );
			$table->decimal( 'costs', 12 );
			$table->decimal( 'rebate', 12 );
			$table->decimal( 'tax', 14, 4 );
			$table->string( 'taxrate' );
			$table->smallint( 'taxflag' );
			$table->int( 'flags' );
			$table->int( 'pos' );
			$table->smallint( 'statuspayment' )->null( true );
			$table->smallint( 'status' )->null( true );
			$table->string( 'notes' );
			$table->meta();

			$table->unique( ['baseid', 'pos'], 'unq_msordbapr_bid_pos' );
			$table->index( ['baseid', 'siteid', 'prodid'], 'idx_msordbapr_bid_sid_pid' );
			$table->index( ['baseid', 'siteid', 'prodcode'], 'idx_msordbapr_bid_sid_pcd' );
			$table->index( ['baseid', 'siteid', 'qtyopen'], 'idx_msordbapr_bid_sid_qtyo' );
			$table->index( ['ctime', 'siteid', 'prodid', 'baseid'], 'idx_msordbapr_ct_sid_pid_bid' );
			$table->index( ['baseid'], 'fk_msordbapr_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msordbapr_baseid' );
		},

		'mshop_order_base_product_attr' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbaprat_id' );
			$table->string( 'siteid' );
			$table->bigint( 'ordprodid' );
			$table->refid( 'attrid' );
			$table->type();
			$table->code()->length( 255 );
			$table->string( 'name' );
			$table->text( 'value' );
			$table->float( 'quantity' );
			$table->meta();

			$table->unique( ['ordprodid', 'attrid', 'type', 'code'], 'unq_msordbaprat_oid_aid_ty_cd' );
			$table->index( ['ordprodid'], 'fk_msordbaprat_ordprodid' );

			$table->foreign( 'ordprodid', 'mshop_order_base_product', 'id', 'fk_msordbaprat_ordprodid' );
		},

		'mshop_order_base_service' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbase_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->refid( 'servid' );
			$table->type();
			$table->code();
			$table->string( 'name' );
			$table->string( 'mediaurl' );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->null( true );
			$table->decimal( 'costs', 12 );
			$table->decimal( 'rebate', 12 );
			$table->decimal( 'tax', 14, 4 );
			$table->string( 'taxrate' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->int( 'pos' )->default( 0 );
			$table->meta();

			$table->unique( ['baseid', 'siteid', 'code', 'type'], 'unq_msordbase_bid_sid_cd_typ' );
			$table->index( ['siteid', 'code', 'type'], 'idx_msordbase_sid_code_type' );
			$table->index( ['baseid'], 'fk_msordbase_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msordbase_baseid' );
		},

		'mshop_order_base_service_attr' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbaseat_id' );
			$table->string( 'siteid' );
			$table->bigint( 'ordservid' );
			$table->refid( 'attrid' );
			$table->type();
			$table->code()->length( 255 );
			$table->string( 'name' );
			$table->text( 'value' );
			$table->float( 'quantity' );
			$table->meta();

			$table->unique( ['ordservid', 'attrid', 'type', 'code'], 'unq_msordbaseat_oid_aid_ty_cd' );
			$table->index( ['ordservid'], 'fk_msordbaseat_ordservid' );

			$table->foreign( 'ordservid', 'mshop_order_base_service', 'id', 'fk_msordbaseat_ordservid' );
		},

		'mshop_order_base_coupon' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbaco_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->bigint( 'ordprodid' )->null( true );
			$table->code();
			$table->meta();

			$table->index( ['baseid', 'siteid', 'code'], 'idx_msordbaco_bid_sid_code' );
			$table->index( ['baseid'], 'fk_msordbaco_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msordbaco_baseid' );
		},

		'mshop_order' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msord_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
			$table->refid( 'relatedid' )->null( true );
			$table->type();
			$table->datetime( 'datepayment' )->null( true );
			$table->datetime( 'datedelivery' )->null( true );
			$table->smallint( 'statuspayment' )->null( true );
			$table->smallint( 'statusdelivery' )->null( true );
			$table->string( 'cdate', 10 );
			$table->string( 'cmonth', 7 );
			$table->string( 'cweek', 7 );
			$table->string( 'cwday', 1 );
			$table->string( 'chour', 2 );
			$table->meta();

			$table->index( ['siteid', 'type'], 'idx_msord_sid_type' );
			$table->index( ['siteid', 'ctime', 'statuspayment'], 'idx_msord_sid_ctime_pstat' );
			$table->index( ['siteid', 'mtime', 'statuspayment'], 'idx_msord_sid_mtime_pstat' );
			$table->index( ['siteid', 'mtime', 'statusdelivery'], 'idx_msord_sid_mtime_dstat' );
			$table->index( ['siteid', 'statusdelivery'], 'idx_msord_sid_dstatus' );
			$table->index( ['siteid', 'datedelivery'], 'idx_msord_sid_ddate' );
			$table->index( ['siteid', 'datepayment'], 'idx_msord_sid_pdate' );
			$table->index( ['siteid', 'editor'], 'idx_msord_sid_editor' );
			$table->index( ['siteid', 'cdate'], 'idx_msord_sid_cdate' );
			$table->index( ['siteid', 'cmonth'], 'idx_msord_sid_cmonth' );
			$table->index( ['siteid', 'cweek'], 'idx_msord_sid_cweek' );
			$table->index( ['siteid', 'cwday'], 'idx_msord_sid_cwday' );
			$table->index( ['siteid', 'chour'], 'idx_msord_sid_chour' );
			$table->index( ['baseid'], 'fk_msord_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msord_baseid' );
		},

		'mshop_order_status' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordst_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->type();
			$table->string( 'value', 64 );
			$table->meta();

			$table->index( ['siteid', 'parentid', 'type', 'value'], 'idx_msordstatus_val_sid' );
			$table->index( ['parentid'], 'fk_msordst_pid' );

			$table->foreign( 'parentid', 'mshop_order', 'id', 'fk_msordst_pid' );
		},
	),
);
