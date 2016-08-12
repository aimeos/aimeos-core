<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'mshop_product_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msproty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msproty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msproty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msproty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msproty_sid_code' );

			return $schema;
		},

		'mshop_product' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mspro_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mspro_siteid_code' );
			$table->addIndex( array( 'id', 'siteid', 'status', 'start', 'end' ), 'idx_mspro_id_sid_stat_st_end' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_mspro_sid_stat_st_end' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mspro_sid_label' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mspro_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mspro_sid_end' );
			$table->addIndex( array( 'typeid' ), 'fk_mspro_typeid' );

			$table->addForeignKeyConstraint( 'mshop_product_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mspro_typeid' );

			return $schema;
		},

		'mshop_product_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msprolity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msprolity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msprolity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msprolity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msprolity_sid_code' );

			return $schema;
		},

		'mshop_product_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_list' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'refid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msproli_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_msproli_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_msproli_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_msproli_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_msproli_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_msproli_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_msproli_pid_sid_pos' );
			$table->addIndex( array( 'typeid' ), 'fk_msproli_typeid' );
			$table->addIndex( array( 'parentid' ), 'fk_msproli_pid' );

			$table->addForeignKeyConstraint( 'mshop_product_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msproli_typeid' );

			$table->addForeignKeyConstraint( 'mshop_product', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msproli_pid' );

			return $schema;
		},

		'mshop_product_property_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_property_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msproprty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msproprty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msproprty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msproprty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msproprty_sid_code' );

			return $schema;
		},

		'mshop_product_property' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_property' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'value', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mspropr_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'typeid', 'langid', 'value' ), 'unq_mspropr_sid_tid_lid_value' );
			$table->addIndex( array( 'siteid', 'langid' ), 'idx_mspropr_sid_langid' );
			$table->addIndex( array( 'siteid', 'value' ), 'idx_mspropr_sid_value' );
			$table->addIndex( array( 'typeid' ), 'fk_mspropr_typeid' );
			$table->addIndex( array( 'parentid' ), 'fk_mspropr_pid' );

			$table->addForeignKeyConstraint( 'mshop_product', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mspropr_pid' );

			$table->addForeignKeyConstraint( 'mshop_product_property_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mspropr_typeid' );

			return $schema;
		},

		'mshop_product_stock_warehouse' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_stock_warehouse' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msprostwa_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_msprostwa_sid_code' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msprostwa_sid_label' );

			return $schema;
		},

		'mshop_product_stock' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_stock' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'warehouseid', 'integer', array() );
			$table->addColumn( 'stocklevel', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'backdate', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msprost_id' );
			$table->addUniqueIndex( array( 'siteid', 'parentid', 'warehouseid' ), 'unq_msprost_sid_pid_wid' );
			$table->addIndex( array( 'siteid', 'stocklevel' ), 'idx_msprost_sid_stocklevel' );
			$table->addIndex( array( 'siteid', 'backdate' ), 'idx_msprost_sid_backdate' );
			$table->addIndex( array( 'warehouseid' ), 'fk_msprost_whid' );
			$table->addIndex( array( 'parentid' ), 'fk_msprost_pid' );

			$table->addForeignKeyConstraint( 'mshop_product', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msprost_pid' );

			$table->addForeignKeyConstraint( 'mshop_product_stock_warehouse', array( 'warehouseid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msprost_whid' );

			return $schema;
		},
	),
);
