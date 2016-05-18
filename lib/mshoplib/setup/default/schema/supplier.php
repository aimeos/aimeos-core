<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_mssup_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mssup_id' );
			return $schema;
		},
		'seq_mssupad_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mssupad_id' );
			return $schema;
		},
		'seq_mssupli_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mssupli_id' );
			return $schema;
		},
		'seq_mssuplity_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mssuplity_id' );
			return $schema;
		},
	),
	'table' => array(

		'mshop_supplier' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 255, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssup_id' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mssup_sid_status' );

			return $schema;
		},

		'mshop_supplier_address' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_address' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'company', 'string', array( 'length' => 100 ) );
			$table->addColumn( 'vatid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'salutation', 'string', array( 'length' => 8 ) );
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
			$table->addColumn( 'pos', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssupad_id' );
			$table->addIndex( array( 'langid' ), 'idx_mssupad_langid' );
			$table->addIndex( array( 'siteid', 'parentid' ), 'idx_mssupad_sid_rid' );

			$table->addForeignKeyConstraint( 'mshop_supplier', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssupad_parentid' );

			return $schema;
		},

		'mshop_supplier_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssuplity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mssuplity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mssuplity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mssuplity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mssuplity_sid_code' );

			return $schema;
		},

		'mshop_supplier_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_list' );

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

			$table->setPrimaryKey( array( 'id' ), 'pk_mssupli_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_mssupli_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_mssupli_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_mssupli_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_mssupli_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_mssupli_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_mssupli_pid_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_supplier_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssupli_typeid' );

			$table->addForeignKeyConstraint( 'mshop_supplier', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssupli_pid' );

			return $schema;
		},
	),
);
