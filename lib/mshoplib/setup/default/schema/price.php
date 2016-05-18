<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_mspri_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mspri_id' );
			return $schema;
		},
		'seq_msprity_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msprity_id' );
			return $schema;
		},
		'seq_msprili_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msprili_id' );
			return $schema;
		},
		'seq_msprility_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msprility_id' );
			return $schema;
		},
	),
	'table' => array(
		'mshop_price_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_price_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msprity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msprity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msprity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msprity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msprity_sid_code' );

			return $schema;
		},

		'mshop_price' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_price' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'quantity', 'integer', array() );
			$table->addColumn( 'value', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 5, 'scale' => 2 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mspri_id' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mspri_sid_label' );
			$table->addIndex( array( 'siteid', 'status', 'currencyid' ), 'idx_mspri_sid_status_currencyid' );
			$table->addIndex( array( 'siteid', 'domain', 'currencyid' ), 'idx_mspri_sid_dom_currid' );
			$table->addIndex( array( 'siteid', 'domain', 'quantity' ), 'idx_mspri_sid_dom_quantity' );
			$table->addIndex( array( 'siteid', 'domain', 'value' ), 'idx_mspri_sid_dom_value' );
			$table->addIndex( array( 'siteid', 'domain', 'costs' ), 'idx_mspri_sid_dom_costs' );
			$table->addIndex( array( 'siteid', 'domain', 'rebate' ), 'idx_mspri_sid_dom_rebate' );
			$table->addIndex( array( 'siteid', 'domain', 'taxrate' ), 'idx_mspri_sid_dom_taxrate' );

			$table->addForeignKeyConstraint( 'mshop_price_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mspri_typeid' );

			return $schema;
		},

		'mshop_price_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_price_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msprility_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msprility_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msprility_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msprility_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msprility_sid_code' );

			return $schema;
		},

		'mshop_price_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_price_list' );

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

			$table->setPrimaryKey( array( 'id' ), 'pk_msprili_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_msprili_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_msprili_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_msprili_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_msprili_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_msprili_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_msprili_pid_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_price_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msprili_typeid' );

			$table->addForeignKeyConstraint( 'mshop_price', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msprirli_pid' );

			return $schema;
		},
	),
);
