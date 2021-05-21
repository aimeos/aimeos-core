<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(

		'mshop_locale_currency' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_currency' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msloccu_id' );
			$table->addIndex( array( 'status' ), 'idx_msloccu_status' );
			$table->addIndex( array( 'label' ), 'idx_msloccu_label' );

			return $schema;
		},

		'mshop_locale_language' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_language' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslocla_id' );
			$table->addIndex( array( 'status' ), 'idx_mslocla_status' );
			$table->addIndex( array( 'label' ), 'idx_mslocla_label' );

			return $schema;
		},

		'mshop_locale_site' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_site' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', ['notnull' => false] );
			$table->addColumn( 'siteid', 'string', ['length' => 255, 'default' => '', 'customSchemaOptions' => ['unique' => true]] );
			$table->addColumn( 'code', 'string', array( 'length' => 255, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255, 'default' => '' ) );
			$table->addColumn( 'icon', 'string', array( 'length' => 255, 'default' => '' ) );
			$table->addColumn( 'logo', 'string', array( 'length' => 255, 'default' => '{}' ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff, 'default' => '{}' ) );
			$table->addColumn( 'supplierid', 'string', array( 'length' => 36, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'theme', 'string', array( 'length' => 32, 'default' => '' ) );
			$table->addColumn( 'level', 'smallint', [] );
			$table->addColumn( 'nleft', 'integer', [] );
			$table->addColumn( 'nright', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslocsi_id' );
			$table->addUniqueIndex( array( 'code' ), 'unq_mslocsi_code' );
			$table->addUniqueIndex( array( 'siteid' ), 'unq_mslocsi_siteid' );
			$table->addIndex( array( 'nleft', 'nright', 'level', 'parentid' ), 'idx_mslocsi_nlt_nrt_lvl_pid' );
			$table->addIndex( array( 'level', 'status' ), 'idx_mslocsi_level_status' );
			$table->addIndex( array( 'label' ), 'idx_mslocsi_label' );

			return $schema;
		},

		'mshop_locale' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'langid', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msloc_id' );
			$table->addUniqueIndex( array( 'siteid', 'langid', 'currencyid' ), 'unq_msloc_sid_lang_curr' );
			$table->addIndex( array( 'siteid', 'currencyid' ), 'idx_msloc_sid_curid' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msloc_sid_status' );
			$table->addIndex( array( 'siteid', 'pos' ), 'idx_msloc_sid_pos' );
			$table->addIndex( array( 'siteid' ), 'fk_msloc_siteid' );
			$table->addIndex( array( 'langid' ), 'fk_msloc_langid' );
			$table->addIndex( array( 'currencyid' ), 'fk_msloc_currid' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'siteid' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_siteid' );

			$table->addForeignKeyConstraint( 'mshop_locale_language', array( 'langid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_langid' );

			$table->addForeignKeyConstraint( 'mshop_locale_currency', array( 'currencyid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_currid' );

			return $schema;
		},
	),
);
