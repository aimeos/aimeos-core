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
			$table->refid( 'customerid' )->default( '' );
			$table->code( 'sitecode' )->length( 255 )->default( '' );
			$table->string( 'langid', 5 );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->default( '0.00' );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.000' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->string( 'customerref' )->default( '' );
			$table->text( 'comment' )->default( '' );
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
			$table->type( 'stocktype' )->default( 'default' );
			$table->string( 'suppliername' )->default( '' );
			$table->refid( 'supplierid' )->default( '' );
			$table->text( 'name' )->default( '' );
			$table->text( 'description' )->default( '' );
			$table->string( 'mediaurl' )->default( '' );
			$table->string( 'target' )->default( '' );
			$table->string( 'timeframe', 16 )->default( '' );
			$table->float( 'quantity' )->default( 1 );
			$table->float( 'qtyopen' )->default( 0 );
			$table->string( 'currencyid', 3 );
			$table->decimal( 'price', 12 )->null( true );
			$table->decimal( 'costs', 12 )->default( '0.00' );
			$table->decimal( 'rebate', 12 )->default( '0.00' );
			$table->decimal( 'tax', 14, 4 )->default( '0.0000' );
			$table->string( 'taxrate' )->default( '{}' );
			$table->smallint( 'taxflag' )->default( 1 );
			$table->int( 'flags' )->default( 0 );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'statuspayment' )->null( true );
			$table->smallint( 'statusdelivery' )->null( true );
			$table->string( 'notes' )->default( '' );
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
			$table->bigint( 'parentid' );
			$table->refid( 'attrid' )->default( '' );
			$table->type();
			$table->code()->length( 255 );
			$table->float( 'quantity' )->default( 1 );
			$table->string( 'name' )->default( '' );
			$table->text( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'attrid', 'type', 'code'], 'unq_msordbaprat_oid_aid_ty_cd' );
			$table->index( ['parentid'], 'fk_msordbaprat_parentid' );

			$table->foreign( 'parentid', 'mshop_order_base_product', 'id', 'fk_msordbaprat_parentid' );
		},

		'mshop_order_base_service' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbase_id' );
			$table->string( 'siteid' );
			$table->bigint( 'baseid' );
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

			$table->unique( ['baseid', 'siteid', 'code', 'type'], 'unq_msordbase_bid_sid_cd_typ' );
			$table->index( ['siteid', 'code', 'type'], 'idx_msordbase_sid_code_type' );
			$table->index( ['baseid'], 'fk_msordbase_baseid' );

			$table->foreign( 'baseid', 'mshop_order_base', 'id', 'fk_msordbase_baseid' );
		},

		'mshop_order_base_service_attr' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msordbaseat_id' );
			$table->string( 'siteid' );
			$table->bigint( 'parentid' );
			$table->refid( 'attrid' )->default( '' );
			$table->type();
			$table->code()->length( 255 );
			$table->float( 'quantity' )->default( 1 );
			$table->string( 'name' )->default( '' );
			$table->text( 'value' );
			$table->meta();

			$table->unique( ['parentid', 'attrid', 'type', 'code'], 'unq_msordbaseat_oid_aid_ty_cd' );
			$table->index( ['parentid'], 'fk_msordbaseat_parentid' );

			$table->foreign( 'parentid', 'mshop_order_base_service', 'id', 'fk_msordbaseat_parentid' );
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
			$table->refid( 'relatedid' )->default( '' );
			$table->type();
			$table->datetime( 'datepayment' )->null( true );
			$table->datetime( 'datedelivery' )->null( true );
			$table->smallint( 'statuspayment' )->null( true );
			$table->smallint( 'statusdelivery' )->null( true );
			$table->string( 'cdate', 10 )->default( '' );
			$table->string( 'cmonth', 7 )->default( '' );
			$table->string( 'cweek', 7 )->default( '' );
			$table->string( 'cwday', 1 )->default( '' );
			$table->string( 'chour', 2 )->default( '' );
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
