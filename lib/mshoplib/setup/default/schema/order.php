<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_msord_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msord_id' );
			return $schema;
		},
		'seq_msordst_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordst_id' );
			return $schema;
		},
		'seq_msordba_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordba_id' );
			return $schema;
		},
		'seq_msordbaad_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbaad_id' );
			return $schema;
		},
		'seq_msordbapr_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbapr_id' );
			return $schema;
		},
		'seq_msordbaprat_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbaprat_id' );
			return $schema;
		},
		'seq_msordbase_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbase_id' );
			return $schema;
		},
		'seq_msordbaseat_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbaseat_id' );
			return $schema;
		},
		'seq_msordbaco_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msordbaco_id' );
			return $schema;
		},
	),
	'table' => array(

		'mshop_order_base' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'customerid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'sitecode', 'string', array( 'length' => 32, 'notnull' => false ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxflag', 'smallint', array() );
			$table->addColumn( 'comment', 'text', array( 'length' => 0xfff ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordba_id' );
			$table->addIndex( array( 'sitecode', 'customerid' ), 'idx_msordba_scode_custid' );
			$table->addIndex( array( 'siteid', 'customerid' ), 'idx_msordba_sid_custid' );
			$table->addIndex( array( 'siteid', 'ctime' ), 'idx_msordba_sid_ctime' );

			return $schema;
		},

		'mshop_order' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'datepayment', 'datetime', array() );
			$table->addColumn( 'datedelivery', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'statuspayment', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'statusdelivery', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'relatedid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
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

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msord_baseid' );

			return $schema;
		},

		'mshop_order_base_address' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_address' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'addrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'salutation', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'company', 'string', array( 'length' => 100 ) );
			$table->addColumn( 'vatid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'title', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'firstname', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'lastname', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'address1', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'address2', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'address3', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'postal', 'string', array( 'length' => 16 ) );
			$table->addColumn( 'city', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'state', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'countryid', 'string', array( 'length' => 2, 'notnull' => false ) );
			$table->addColumn( 'telephone', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'email', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'telefax', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'website', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'flag', 'integer', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaad_id' );
			$table->addUniqueIndex( array( 'baseid', 'type' ), 'unq_msordbaad_bid_type' );
			$table->addIndex( array( 'siteid', 'baseid', 'type' ), 'idx_msordbaad_sid_bid_typ' );
			$table->addIndex( array( 'baseid', 'siteid', 'lastname' ), 'idx_msordbaad_bid_sid_lname' );
			$table->addIndex( array( 'baseid', 'siteid', 'address1' ), 'idx_msordbaad_bid_sid_addr1' );
			$table->addIndex( array( 'baseid', 'siteid', 'postal' ), 'idx_msordbaad_bid_sid_postal' );
			$table->addIndex( array( 'baseid', 'siteid', 'city' ), 'idx_msordbaad_bid_sid_city' );
			$table->addIndex( array( 'baseid', 'siteid', 'email' ), 'idx_msordbaad_bid_sid_email' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaad_baseid' );

			return $schema;
		},

		'mshop_order_base_product' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_product' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'prodid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'prodcode', 'string', array( 'length' => 32  ) );
			$table->addColumn( 'suppliercode', 'string', array( 'length' => 32  ) );
			$table->addColumn( 'warehousecode', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mediaurl', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'quantity', 'integer', array() );
			$table->addColumn( 'price', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'tax', 'decimal', array( 'precision' => 14, 'scale' => 4 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 5, 'scale' => 2 ) );
			$table->addColumn( 'taxflag', 'smallint', array() );
			$table->addColumn( 'flags', 'integer', array() );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array( 'default' => -1 ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbapr_id' );
			$table->addUniqueIndex( array( 'baseid', 'pos' ), 'unq_msordbapr_bid_pos' );
			$table->addIndex( array( 'siteid', 'baseid', 'prodcode' ), 'idx_msordbapr_sid_bid_pcd' );
			$table->addIndex( array( 'siteid', 'ctime', 'prodid', 'baseid' ), 'idx_msordbapr_sid_ct_pid_bid' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbapr_baseid' );

			return $schema;
		},

		'mshop_order_base_product_attr' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_product_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordprodid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaprat_id' );
			$table->addUniqueIndex( array( 'ordprodid', 'type', 'code' ), 'unq_msordbaprat_opid_type_code' );

			$table->addForeignKeyConstraint( 'mshop_order_base_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaprat_ordprodid' );

			return $schema;
		},

		'mshop_order_base_service' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_service' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', array() );
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
			$table->addColumn( 'taxflag', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbase_id' );
			$table->addUniqueIndex( array( 'baseid', 'type', 'code' ), 'unq_msordbase_bid_type_code' );
			$table->addIndex( array( 'siteid', 'baseid', 'code', 'type' ), 'idx_msordbase_sid_bid_cd_typ' );
			$table->addIndex( array( 'siteid', 'code', 'type' ), 'idx_msordbase_sid_code_type' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbase_baseid' );

			return $schema;
		},

		'mshop_order_base_service_attr' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_service_attr' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'ordservid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'attrid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'name', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaseat_id' );
			$table->addUniqueIndex( array( 'ordservid', 'type', 'code' ), 'unq_msordbaseat_osid_type_code' );

			$table->addForeignKeyConstraint( 'mshop_order_base_service', array( 'ordservid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaseat_ordservid' );

			return $schema;
		},

		'mshop_order_base_coupon' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_base_coupon' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'baseid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', array( 'notnull' => false ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordbaco_id' );
			$table->addIndex( array( 'siteid', 'baseid', 'code' ), 'idx_msordbaco_sid_bid_code' );

			$table->addForeignKeyConstraint( 'mshop_order_base', array( 'baseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaco_baseid' );

			$table->addForeignKeyConstraint( 'mshop_order_base_product', array( 'ordprodid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordbaco_ordprodid' );

			return $schema;
		},

		'mshop_order_status' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_order_status' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'bigint', array() );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'value', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msordst_id' );
			$table->addIndex( array( 'siteid', 'parentid', 'type', 'value' ), 'idx_msordstatus_val_sid' );

			$table->addForeignKeyConstraint( 'mshop_order', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msordst_parentid' );

			return $schema;
		},
	),
);
