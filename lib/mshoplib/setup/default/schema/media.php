<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_msmed_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msmed_id' );
			return $schema;
		},
		'seq_msmedty_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msmedty_id' );
			return $schema;
		},
		'seq_msmedli_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msmedli_id' );
			return $schema;
		},
		'seq_msmedlity_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msmedlity_id' );
			return $schema;
		},
	),
	'table' => array(
		'mshop_media_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_media_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msmedty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msmedty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msmedty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msmedty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msmedty_sid_code' );

			return $schema;
		},

		'mshop_media' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_media' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'typeid', 'integer', array() );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'link', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'preview', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mimetype', 'string', array( 'length' => 64 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msmed_id' );
			$table->addIndex( array( 'langid' ), 'idx_msmed_langid' );
			$table->addIndex( array( 'siteid', 'status', 'langid' ), 'idx_msmed_sid_status_langid' );
			$table->addIndex( array( 'siteid', 'domain', 'langid' ), 'idx_msmed_sid_dom_langid' );
			$table->addIndex( array( 'siteid', 'domain', 'label' ), 'idx_msmed_sid_dom_label' );
			$table->addIndex( array( 'siteid', 'domain', 'mimetype' ), 'idx_msmed_sid_dom_mime' );
			$table->addIndex( array( 'siteid', 'domain', 'link' ), 'idx_msmed_sid_dom_link' );

			$table->addForeignKeyConstraint( 'mshop_media_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msmed_typeid' );

			return $schema;
		},

		'mshop_media_list_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_media_list_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msmedlity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msmedlity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msmedlity_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msmedlity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msmedlity_sid_code' );

			return $schema;
		},

		'mshop_media_list' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_media_list' );

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

			$table->setPrimaryKey( array( 'id' ), 'pk_msmedli_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'refid', 'typeid', 'parentid' ), 'unq_msmedli_sid_dm_rid_tid_pid' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_msmedli_sid_stat_start_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'refid', 'domain', 'typeid' ), 'idx_msmedli_pid_sid_rid_dm_tid' );
			$table->addIndex( array( 'parentid', 'siteid', 'start' ), 'idx_msmedli_pid_sid_start' );
			$table->addIndex( array( 'parentid', 'siteid', 'end' ), 'idx_msmedli_pid_sid_end' );
			$table->addIndex( array( 'parentid', 'siteid', 'pos' ), 'idx_msmedli_pid_sid_pos' );

			$table->addForeignKeyConstraint( 'mshop_media_list_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msmedli_typeid' );

			$table->addForeignKeyConstraint( 'mshop_media', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msmedrli_pid' );

			return $schema;
		},
	),
);
