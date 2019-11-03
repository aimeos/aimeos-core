<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 */


return array(

	'exclude' => array(
		'idx_mordprat_si_cd_va',
		'idx_mordseat_si_cd_va',
	),


	'table' => array(

		'mshop_order' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'sitecode', 'string', array( 'length' => 255, 'notnull' => false, 'default' => '' ) );
			$table->addColumn( 'relatedid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'customerid', 'string', array( 'length' => 36, 'default' => '' ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'default' => '' ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'default' => '' ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3, 'default' => '' ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2, 'default' => '0.00' ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2, 'default' => '0.00' ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2, 'default' => '0.00' ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4, 'default' => '0.0000' ) );
			$table->addColumn( 'taxflag', 'smallint', ['default' => 0] );
			$table->addColumn( 'datepayment', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'datedelivery', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'statuspayment', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'statusdelivery', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'customerref', 'string', array( 'length' => 255, 'notnull' => false, 'default' => '' ) );
			$table->addColumn( 'comment', 'text', array( 'length' => 0xfff, 'default' => '' ) );
			$table->addColumn( 'cdate', 'string', array( 'length' => 10 ) );
			$table->addColumn( 'cmonth', 'string', array( 'length' => 7 ) );
			$table->addColumn( 'cweek', 'string', array( 'length' => 7 ) );
			$table->addColumn( 'cwday', 'string', array( 'length' => 1 ) );
			$table->addColumn( 'chour', 'string', array( 'length' => 2 ) );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msord_id' );
			$table->addIndex( array( 'siteid', 'type' ), 'idx_msord_sid_type' );
			$table->addIndex( array( 'siteid', 'customerid' ), 'idx_mord_sid_custid' );
			$table->addIndex( array( 'sitecode', 'customerid' ), 'idx_mord_scode_custid' );
			$table->addIndex( array( 'siteid', 'mtime', 'statuspayment' ), 'idx_msord_sid_mtime_pstat' );
			$table->addIndex( array( 'siteid', 'mtime', 'statusdelivery' ), 'idx_msord_sid_mtime_dstat' );
			$table->addIndex( array( 'siteid', 'statusdelivery' ), 'idx_msord_sid_dstatus' );
			$table->addIndex( array( 'siteid', 'datedelivery' ), 'idx_msord_sid_ddate' );
			$table->addIndex( array( 'siteid', 'datepayment' ), 'idx_msord_sid_pdate' );
			$table->addIndex( array( 'siteid', 'editor' ), 'idx_msord_sid_editor' );
			$table->addIndex( array( 'siteid', 'ctime' ), 'idx_msord_sid_ctime' );
			$table->addIndex( array( 'siteid', 'cdate' ), 'idx_msord_sid_cdate' );
			$table->addIndex( array( 'siteid', 'cmonth' ), 'idx_msord_sid_cmonth' );
			$table->addIndex( array( 'siteid', 'cweek' ), 'idx_msord_sid_cweek' );
			$table->addIndex( array( 'siteid', 'cwday' ), 'idx_msord_sid_cwday' );
			$table->addIndex( array( 'siteid', 'chour' ), 'idx_msord_sid_chour' );

			return $schema;
		},

		'mshop_order_address' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_address' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'orderid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'addrid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'salutation', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'company', 'string', array( 'length' => 100 ) );
			$table->addColumn( 'vatid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'title', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'firstname', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'lastname', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'address1', 'string', array( 'length' => 200 ) );
			$table->addColumn( 'address2', 'string', array( 'length' => 200 ) );
			$table->addColumn( 'address3', 'string', array( 'length' => 200 ) );
			$table->addColumn( 'postal', 'string', array( 'length' => 16 ) );
			$table->addColumn( 'city', 'string', array( 'length' => 200 ) );
			$table->addColumn( 'state', 'string', array( 'length' => 200 ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'countryid', 'string', array( 'length' => 2, 'notnull' => false ) );
			$table->addColumn( 'telephone', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'telefax', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'email', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'website', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'longitude', 'decimal', array( 'precision' => 8, 'scale' => 6, 'notnull' => false ) );
			$table->addColumn( 'latitude', 'decimal', array( 'precision' => 8, 'scale' => 6, 'notnull' => false ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordad_id' );
			$table->addUniqueIndex( array( 'orderid', 'type' ), 'unq_mordad_oid_type' );
			$table->addIndex( array( 'siteid', 'orderid', 'type' ), 'idx_mordad_sid_oid_typ' );
			$table->addIndex( array( 'orderid', 'siteid', 'lastname' ), 'idx_mordad_oid_sid_lname' );
			$table->addIndex( array( 'orderid', 'siteid', 'address1' ), 'idx_mordad_oid_sid_addr1' );
			$table->addIndex( array( 'orderid', 'siteid', 'postal' ), 'idx_mordad_oid_sid_postal' );
			$table->addIndex( array( 'orderid', 'siteid', 'city' ), 'idx_mordad_oid_sid_city' );
			$table->addIndex( array( 'orderid', 'siteid', 'email' ), 'idx_mordad_oid_sid_email' );
			$table->addIndex( array( 'orderid' ), 'fk_mordad_orderid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'orderid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordad_orderid' );

			return $schema;
		},

		'mshop_order_product' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_product' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'orderid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'ordaddrid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'prodid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'prodcode', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'suppliercode', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'stocktype', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'name', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'description', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mediaurl', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'target', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'timeframe', 'string', array( 'length' => 16 ) );
			$table->addColumn( 'quantity', 'integer', [] );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxrate', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'taxflag', 'smallint', [] );
			$table->addColumn( 'flags', 'integer', [] );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordpr_id' );
			$table->addUniqueIndex( array( 'orderid', 'pos' ), 'unq_mordpr_oid_pos' );
			$table->addIndex( array( 'siteid', 'orderid', 'prodcode' ), 'idx_mordpr_sid_oid_pcd' );
			$table->addIndex( array( 'siteid', 'ctime', 'prodid', 'orderid' ), 'idx_mordpr_sid_ct_pid_oid' );
			$table->addIndex( array( 'orderid' ), 'fk_mordpr_orderid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'orderid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordpr_orderid' );

			return $schema;
		},

		'mshop_order_product_attr' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_product_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordprodid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'quantity', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordprat_id' );
			$table->addUniqueIndex( array( 'ordprodid', 'attrid', 'type', 'code' ), 'unq_mordprat_oid_aid_ty_cd' );
			$table->addIndex( array( 'ordprodid' ), 'fk_mordprat_ordprodid' );

			$table->addForeignKeyConstraint( 'mshop_order_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordprat_ordprodid' );

			return $schema;
		},

		'mshop_order_service' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_service' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'orderid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'servid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mediaurl', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxrate', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'taxflag', 'smallint', [] );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordse_id' );
			$table->addUniqueIndex( array( 'siteid', 'orderid', 'code', 'type' ), 'unq_mordse_sid_oid_cd_typ' );
			$table->addIndex( array( 'siteid', 'code', 'type' ), 'idx_mordse_sid_code_type' );
			$table->addIndex( array( 'orderid' ), 'fk_mordse_orderid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'orderid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordse_orderid' );

			return $schema;
		},

		'mshop_order_service_attr' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_service_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordservid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 36 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'quantity', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordseat_id' );
			$table->addUniqueIndex( array( 'ordservid', 'attrid', 'type', 'code' ), 'unq_mordseat_oid_aid_ty_cd' );
			$table->addIndex( array( 'ordservid' ), 'fk_mordseat_ordservid' );

			$table->addForeignKeyConstraint( 'mshop_order_service', array( 'ordservid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordseat_ordservid' );

			return $schema;
		},

		'mshop_order_coupon' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_coupon' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'orderid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mordco_id' );
			$table->addIndex( array( 'siteid', 'orderid', 'code' ), 'idx_mordco_sid_oid_code' );
			$table->addIndex( array( 'ordprodid' ), 'fk_mordco_ordprodid' );
			$table->addIndex( array( 'orderid' ), 'fk_mordco_orderid' );

			$table->addForeignKeyConstraint( 'mshop_order_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordco_ordprodid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'orderid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mordco_orderid' );

			return $schema;
		},

		'mshop_order_status' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_status' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'value', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordst_id' );
			$table->addIndex( array( 'siteid', 'parentid', 'type', 'value' ), 'idx_msordstatus_val_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_msordst_pid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordst_pid' );

			return $schema;
		},
	),
);
