<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_msatt_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msatt_id' );
			return $schema;
		},
		'seq_msattty_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msattty_id' );
			return $schema;
		},
		'seq_msattli_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msattli_id' );
			return $schema;
		},
		'seq_msattlity_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msattlity_id' );
			return $schema;
		},
	),
	'table' => array(
		'mshop_attribute_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msattty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msattty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msattty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msattty_sid_code' );

			return $schema;
		},

		'mshop_attribute' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msatt_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code', 'typeid' ), 'unq_msattr_sid_dom_cod_tid' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msatt_sid_status' );
			$table->addIndex( array( 'siteid', 'domain', 'label' ), 'idx_msatt_sid_dom_label' );
			$table->addIndex( array( 'siteid', 'domain', 'pos' ), 'idx_msatt_sid_dom_pos' );

			$table->addForeignKeyConstraint( 'mshop_attribute_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msatt_typeid' );

			return $schema;
		},

		'mshop_attribute_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msattlity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msattlity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msattlity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msattlity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msattlity_sid_code' );

			return $schema;
		},

		'mshop_attribute_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_attribute_list' );

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

			$table->setPrimaryKey( array( 'id' ), 'pk_msattli_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_msattli_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_msattli_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_msattli_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_msattli_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_msattli_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_msattli_pid_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_attribute_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msattli_typeid' );

			$table->addForeignKeyConstraint( 'mshop_attribute', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msattli_pid' );

			return $schema;
		},
	),
);
