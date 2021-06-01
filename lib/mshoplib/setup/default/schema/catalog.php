<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(

		'mshop_catalog' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_catalog' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', ['notnull' => false] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'level', 'smallint', [] );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'url', 'string', array( 'length' => 255, 'default' => '' ) );
			$table->addColumn( 'config', 'text', array( 'default' => '', 'length' => 0xffff ) );
			$table->addColumn( 'nleft', 'integer', [] );
			$table->addColumn( 'nright', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'target', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscat_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mscat_sid_code' );
			$table->addIndex( array( 'siteid', 'nleft', 'nright', 'level', 'parentid' ), 'idx_mscat_sid_nlt_nrt_lvl_pid' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mscat_sid_status' );

			return $schema;
		},

		'mshop_catalog_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_catalog_list_type' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscatlity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mscatlity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_mscatlity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscatlity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mscatlity_sid_code' );

			return $schema;
		},

		'mshop_catalog_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_catalog_list' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'key', 'string', array( 'length' => 134, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'refid', 'string', array( 'length' => 36, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscatli_id' );
			$table->addUniqueIndex( array( 'parentid', 'domain', 'siteid', 'type', 'refid' ), 'unq_mscatli_pid_dm_sid_ty_rid' );
			$table->addIndex( array( 'parentid', 'domain', 'siteid', 'pos', 'refid' ), 'idx_mscatli_pid_dm_sid_pos_rid' );
			$table->addIndex( array( 'refid', 'domain', 'siteid', 'type' ), 'idx_mscatli_rid_dom_sid_ty' );
			$table->addIndex( array( 'key', 'siteid' ), 'idx_mscatli_key_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_mscatli_pid' );

			$table->addForeignKeyConstraint( 'mshop_catalog', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscatli_pid' );

			return $schema;
		},
	),
);
