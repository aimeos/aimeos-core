<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(

	'exclude' => array(
		'idx_msordbaprat_si_cd_va',
		'idx_msordbaseat_si_cd_va',
	),


	'table' => array(

		'mshop_order_base' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'customerid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'sitecode', 'string', array( 'length' => 32, 'notnull' => false ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3, 'fixed' => true ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxflag', 'smallint', [] );
			$table->addColumn( 'comment', 'text', array( 'length' => 0xfff ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordba_id' );
			$table->addIndex( array( 'sitecode', 'customerid' ), 'idx_msordba_scode_custid' );
			$table->addIndex( array( 'siteid', 'customerid' ), 'idx_msordba_sid_custid' );
			$table->addIndex( array( 'siteid', 'ctime' ), 'idx_msordba_sid_ctime' );

			return $schema;
		},

		'mshop_order_base_address' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_address' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'addrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 8 ) );
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
			$table->addColumn( 'countryid', 'string', array( 'length' => 2, 'notnull' => false, 'fixed' => true ) );
			$table->addColumn( 'telephone', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'telefax', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'email', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'website', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'longitude', 'decimal', array( 'precision' => 8, 'scale' => 6, 'notnull' => false ) );
			$table->addColumn( 'latitude', 'decimal', array( 'precision' => 8, 'scale' => 6, 'notnull' => false ) );
			$table->addColumn( 'flag', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaad_id' );
			$table->addUniqueIndex( array( 'baseid', 'type' ), 'unq_msordbaad_bid_type' );
			$table->addIndex( array( 'siteid', 'baseid', 'type' ), 'idx_msordbaad_sid_bid_typ' );
			$table->addIndex( array( 'baseid', 'siteid', 'lastname' ), 'idx_msordbaad_bid_sid_lname' );
			$table->addIndex( array( 'baseid', 'siteid', 'address1' ), 'idx_msordbaad_bid_sid_addr1' );
			$table->addIndex( array( 'baseid', 'siteid', 'postal' ), 'idx_msordbaad_bid_sid_postal' );
			$table->addIndex( array( 'baseid', 'siteid', 'city' ), 'idx_msordbaad_bid_sid_city' );
			$table->addIndex( array( 'baseid', 'siteid', 'email' ), 'idx_msordbaad_bid_sid_email' );
			$table->addIndex( array( 'baseid' ), 'fk_msordbaad_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaad_baseid' );

			return $schema;
		},

		'mshop_order_base_product' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_product' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'prodid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'prodcode', 'string', array( 'length' => 32  ) );
			$table->addColumn( 'suppliercode', 'string', array( 'length' => 32  ) );
			$table->addColumn( 'stocktype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mediaurl', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'quantity', 'integer', [] );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 5, 'scale' => 2 ) );
			$table->addColumn( 'taxflag', 'smallint', [] );
			$table->addColumn( 'flags', 'integer', [] );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbapr_id' );
			$table->addUniqueIndex( array( 'baseid', 'pos' ), 'unq_msordbapr_bid_pos' );
			$table->addIndex( array( 'siteid', 'baseid', 'prodcode' ), 'idx_msordbapr_sid_bid_pcd' );
			$table->addIndex( array( 'siteid', 'ctime', 'prodid', 'baseid' ), 'idx_msordbapr_sid_ct_pid_bid' );
			$table->addIndex( array( 'baseid' ), 'fk_msordbapr_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbapr_baseid' );

			return $schema;
		},

		'mshop_order_base_product_attr' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_product_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordprodid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaprat_id' );
			$table->addUniqueIndex( array( 'ordprodid', 'type', 'code' ), 'unq_msordbaprat_opid_type_code' );
			$table->addIndex( array( 'ordprodid' ), 'fk_msordbaprat_ordprodid' );

			$table->addForeignKeyConstraint( 'mshop_order_base_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaprat_ordprodid' );

			return $schema;
		},

		'mshop_order_base_service' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_service' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'servid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mediaurl', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 5, 'scale' => 2 ) );
			$table->addColumn( 'taxflag', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbase_id' );
			$table->addUniqueIndex( array( 'baseid', 'type', 'code' ), 'unq_msordbase_bid_type_code' );
			$table->addIndex( array( 'siteid', 'baseid', 'code', 'type' ), 'idx_msordbase_sid_bid_cd_typ' );
			$table->addIndex( array( 'siteid', 'code', 'type' ), 'idx_msordbase_sid_code_type' );
			$table->addIndex( array( 'baseid' ), 'fk_msordbase_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbase_baseid' );

			return $schema;
		},

		'mshop_order_base_service_attr' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_service_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordservid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaseat_id' );
			$table->addUniqueIndex( array( 'ordservid', 'type', 'code' ), 'unq_msordbaseat_osid_type_code' );
			$table->addIndex( array( 'ordservid' ), 'fk_msordbaseat_ordservid' );

			$table->addForeignKeyConstraint( 'mshop_order_base_service', array( 'ordservid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaseat_ordservid' );

			return $schema;
		},

		'mshop_order_base_coupon' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_coupon' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaco_id' );
			$table->addIndex( array( 'siteid', 'baseid', 'code' ), 'idx_msordbaco_sid_bid_code' );
			$table->addIndex( array( 'ordprodid' ), 'fk_msordbaco_ordprodid' );
			$table->addIndex( array( 'baseid' ), 'fk_msordbaco_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaco_ordprodid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaco_baseid' );

			return $schema;
		},

		'mshop_order' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'datepayment', 'datetime', [] );
			$table->addColumn( 'datedelivery', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'statuspayment', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'statusdelivery', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'relatedid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'cdate', 'string', array( 'fixed' => 10 ) );
			$table->addColumn( 'cweek', 'string', array( 'fixed' => 7 ) );
			$table->addColumn( 'cmonth', 'string', array( 'fixed' => 7 ) );
			$table->addColumn( 'chour', 'string', array( 'fixed' => 2 ) );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msord_id' );
			$table->addIndex( array( 'siteid', 'type' ), 'idx_msord_sid_type' );
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
			$table->addIndex( array( 'siteid', 'chour' ), 'idx_msord_sid_chour' );
			$table->addIndex( array( 'baseid' ), 'fk_msord_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msord_baseid' );

			return $schema;
		},

		'mshop_order_status' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_status' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'bigint', [] );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'value', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordst_id' );
			$table->addIndex( array( 'siteid', 'parentid', 'type', 'value' ), 'idx_msordstatus_val_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_msordst_pid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordst_pid' );

			return $schema;
		},
	),
);
