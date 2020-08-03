<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


return array(
	'table' => array(
		'mshop_customer' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255, 'default' => ''] );
			$table->addColumn( 'code', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'salutation', 'string', array( 'length' => 8 ) );
			$table->addColumn( 'company', 'string', array( 'length' => 100 ) );
			$table->addColumn( 'vatid', 'string', array( 'length' => 32 ) );
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
			$table->addColumn( 'vdate', 'date', array( 'notnull' => false ) );
			$table->addColumn( 'password', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscus_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mscus_sid_code' );
			$table->addIndex( array( 'siteid', 'langid' ), 'idx_mscus_sid_langid' );
			$table->addIndex( array( 'siteid', 'lastname', 'firstname' ), 'idx_mscus_sid_last_first' );
			$table->addIndex( array( 'siteid', 'postal', 'address1' ), 'idx_mscus_sid_post_addr1' );
			$table->addIndex( array( 'siteid', 'postal', 'city' ), 'idx_mscus_sid_post_city' );
			$table->addIndex( array( 'siteid', 'city' ), 'idx_mscus_sid_city' );
			$table->addIndex( array( 'siteid', 'email' ), 'idx_mscus_sid_email' );

			return $schema;
		},

		'mshop_customer_address' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_address' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255, 'default' => ''] );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mscusad_id' );
			$table->addIndex( array( 'parentid' ), 'fk_mscusad_pid' );
			$table->addIndex( array( 'langid' ), 'idx_mscusad_langid' );
			$table->addIndex( array( 'siteid', 'lastname', 'firstname' ), 'idx_mscusad_sid_last_first' );
			$table->addIndex( array( 'siteid', 'postal', 'address1' ), 'idx_mscusad_sid_post_addr1' );
			$table->addIndex( array( 'siteid', 'postal', 'city' ), 'idx_mscusad_sid_post_ci' );
			$table->addIndex( array( 'siteid', 'city' ), 'idx_mscusad_sid_city' );
			$table->addIndex( array( 'siteid', 'email' ), 'idx_mscusad_sid_email' );

			$table->addForeignKeyConstraint( 'mshop_customer', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscusad_pid' );

			return $schema;
		},

		'mshop_customer_group' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_group' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255, 'default' => ''] );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscusgr_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mscusgr_sid_code' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscusgr_sid_label' );

			return $schema;
		},

		'mshop_customer_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_list_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mscuslity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mscuslity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_mscuslity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscuslity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mscuslity_sid_code' );

			return $schema;
		},

		'mshop_customer_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_list' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'parentid', 'integer', [] );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mscusli_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'domain', 'type', 'refid' ), 'unq_mscusli_pid_sid_dm_ty_rid' );
			$table->addIndex( array( 'siteid', 'key' ), 'idx_mscusli_sid_key' );
			$table->addIndex( array( 'parentid' ), 'fk_mscusli_pid' );

			$table->addForeignKeyConstraint( 'mshop_customer', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscusli_pid' );

			return $schema;
		},

		'mshop_customer_property_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_property_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mcusprty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mcusprty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_mcusprty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mcusprty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mcusprty_sid_code' );

			return $schema;
		},

		'mshop_customer_property' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_customer_property' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'key', 'string', array( 'length' => 103, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'value', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mcuspr_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'type', 'langid', 'value' ), 'unq_mcuspr_sid_ty_lid_value' );
			$table->addIndex( array( 'siteid', 'key' ), 'fk_mscuspr_sid_key' );
			$table->addIndex( array( 'parentid' ), 'fk_mcuspr_pid' );

			$table->addForeignKeyConstraint( 'mshop_customer', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mcuspr_pid' );

			return $schema;
		},
	),
);
