<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_msser_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msser_id' );
			return $schema;
		},
		'seq_msserty_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msserty_id' );
			return $schema;
		},
		'seq_msserli_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msserli_id' );
			return $schema;
		},
		'seq_msserlity_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msserlity_id' );
			return $schema;
		},
	),
	'table' => array(
		'mshop_service_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_service_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msserty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msserty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msserty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msserty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msserty_sid_code' );

			return $schema;
		},

		'mshop_service' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_service' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 255, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'provider', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msser_id' );
			$table->addUniqueIndex( array( 'siteid', 'typeid', 'code' ), 'unq_msser_siteid_typeid_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msser_sid_status' );
			$table->addIndex( array( 'siteid', 'provider' ), 'idx_msser_sid_prov' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msser_sid_code' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msser_sid_label' );
			$table->addIndex( array( 'siteid', 'pos' ), 'idx_msser_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_service_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msser_typeid' );

			return $schema;
		},

		'mshop_service_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_service_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msserlity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msserlity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msserlity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msserlity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msserlity_sid_code' );

			return $schema;
		},

		'mshop_service_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_service_list' );

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

			$table->setPrimaryKey( array( 'id' ), 'pk_msserli_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_msserli_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_msserli_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_msserli_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_msserli_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_msserli_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_msserli_pid_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_service_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msserli_typeid' );

			$table->addForeignKeyConstraint( 'mshop_service', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msserli_pid' );

			return $schema;
		},
	),
);
