<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_product_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_msproty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msproty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msproty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msproty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msproty_sid_code' );

			return $schema;
		},

		'mshop_product' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'dataset', 'string', array( 'length' => 64, 'default' => '' ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255, 'default' => '' ) );
			$table->addColumn( 'url', 'string', array( 'length' => 255, 'default' => '' ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'scale', 'float', ['default' => 1] );
			$table->addColumn( 'rating', 'decimal', ['precision' => 4, 'scale' => 2, 'default' => 0] );
			$table->addColumn( 'ratings', 'integer', ['default' => 0] );
			$table->addColumn( 'instock', 'smallint', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'target', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mspro_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mspro_siteid_code' );
			$table->addIndex( array( 'id', 'siteid', 'status', 'start', 'end', 'rating' ), 'idx_mspro_id_sid_stat_st_end_rt' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end', 'rating' ), 'idx_mspro_sid_stat_st_end_rt' );
			$table->addIndex( array( 'siteid', 'rating' ), 'idx_mspro_sid_rating' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mspro_sid_label' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mspro_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mspro_sid_end' );

			return $schema;
		},

		'mshop_product_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_list_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_msprolity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msprolity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msprolity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msprolity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msprolity_sid_code' );

			return $schema;
		},

		'mshop_product_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_list' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_msproli_id' );
			$table->addUniqueIndex( array( 'parentid', 'domain', 'siteid', 'type', 'refid' ), 'unq_msproli_pid_dm_sid_ty_rid' );
			$table->addIndex( array( 'key', 'siteid' ), 'idx_msproli_key_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_msproli_pid' );

			$table->addForeignKeyConstraint( 'mshop_product', array( 'parentid' ), array( 'id' ),
					array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msproli_pid' );

			return $schema;
		},

		'mshop_product_property_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_property_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_msproprty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msproprty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msproprty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msproprty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msproprty_sid_code' );

			return $schema;
		},

		'mshop_product_property' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_product_property' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_mspropr_id' );
			$table->addUniqueIndex( array( 'parentid', 'siteid', 'type', 'langid', 'value' ), 'unq_mspropr_sid_ty_lid_value' );
			$table->addIndex( array( 'key', 'siteid' ), 'fk_mspropr_key_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_mspropr_pid' );

			$table->addForeignKeyConstraint( 'mshop_product', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mspropr_pid' );

			return $schema;
		},
	),
);
