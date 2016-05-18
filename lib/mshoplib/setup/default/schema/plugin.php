<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_msplu_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msplu_id' );
			return $schema;
		},
		'seq_mspluty_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mspluty_id' );
			return $schema;
		},
	),
	'table' => array(
		'mshop_plugin_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_plugin_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mspluty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mspluty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mspluty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mspluty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mspluty_sid_code' );

			return $schema;
		},

		'mshop_plugin' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_plugin' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'provider', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msplu_id' );
			$table->addUniqueIndex( array( 'siteid', 'typeid', 'provider' ), 'unq_mspul_sid_tid_provider' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msplu_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msplu_sid_label' );
			$table->addIndex( array( 'siteid', 'provider' ), 'idx_msplu_sid_provider' );
			$table->addIndex( array( 'siteid', 'pos' ), 'idx_msplu_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_plugin_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msplu_typeid' );

			return $schema;
		},
	),
);
