<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


return array(
	'table' => array(

		'mshop_supplier' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssup_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mssup_sid_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mssup_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mssup_sid_label' );

			return $schema;
		},

		'mshop_supplier_address' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_address' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'company', 'string', array( 'length' => 100 ) );
			$table->addColumn( 'vatid', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'salutation', 'string', array( 'length' => 8 ) );
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
			$table->addColumn( 'countryid', 'string', array( 'length' => 2, 'notnull' => false ) );
			$table->addColumn( 'telephone', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'telefax', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'email', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'website', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'longitude', 'float', array( 'notnull' => false ) );
			$table->addColumn( 'latitude', 'float', array( 'notnull' => false ) );
			$table->addColumn( 'birthday', 'date', array( 'notnull' => false ) );
			$table->addColumn( 'pos', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssupad_id' );
			$table->addIndex( array( 'siteid', 'parentid' ), 'idx_mssupad_sid_rid' );
			$table->addIndex( array( 'parentid' ), 'fk_mssupad_pid' );

			$table->addForeignKeyConstraint( 'mshop_supplier', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssupad_pid' );

			return $schema;
		},

		'mshop_supplier_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_list_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mssuplity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mssuplity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_mssuplity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mssuplity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mssuplity_sid_code' );

			return $schema;
		},

		'mshop_supplier_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_supplier_list' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mssupli_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'domain', 'type', 'refid' ), 'unq_mssupli_pid_sid_dm_ty_rid' );
			$table->addIndex( array( 'parentid', 'siteid', 'domain', 'pos', 'refid' ), 'idx_mssupli_pid_sid_dm_pos_rid' );
			$table->addIndex( array( 'siteid', 'key' ), 'idx_mssupli_sid_key' );
			$table->addIndex( array( 'parentid' ), 'fk_mssupli_pid' );

			$table->addForeignKeyConstraint( 'mshop_supplier', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssupli_pid' );

			return $schema;
		},
	),
);
