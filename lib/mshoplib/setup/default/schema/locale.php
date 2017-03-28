<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(

		'mshop_locale_site' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_site' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'default' => '', 'length' => 0xffff ) );
			$table->addColumn( 'level', 'smallint', [] );
			$table->addColumn( 'nleft', 'integer', [] );
			$table->addColumn( 'nright', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslocsi_id' );
			$table->addUniqueIndex( array( 'code' ), 'unq_mslocsi_code' );
			$table->addIndex( array( 'nleft', 'nright', 'level', 'parentid' ), 'idx_mslocsi_nlt_nrt_lvl_pid' );
			$table->addIndex( array( 'level', 'status' ), 'idx_mslocsi_level_status' );

			return $schema;
		},

		'mshop_locale_language' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_language' );

			$table->addColumn( 'id', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslocla_id' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mslocla_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mslocla_sid_label' );
			$table->addIndex( array( 'siteid' ), 'fk_mslocla_siteid' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL' ), 'fk_mslocla_siteid' );

			return $schema;
		},

		'mshop_locale_currency' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_currency' );

			$table->addColumn( 'id', 'string', array( 'length' => 3, 'fixed' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msloccu_id' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msloccu_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mslocla_label' );
			$table->addIndex( array( 'siteid' ), 'fk_msloccu_siteid' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL' ), 'fk_msloccu_siteid' );

			return $schema;
		},

		'mshop_locale' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'langid', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3, 'fixed' => true ) );
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
			$table->addIndex( array( 'siteid' ), 'fk_mslocsi_id' );
			$table->addIndex( array( 'siteid' ), 'fk_mslocla_id' );
			$table->addIndex( array( 'siteid' ), 'fk_msloccu_id' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_siteid' );

			$table->addForeignKeyConstraint( 'mshop_locale_language', array( 'langid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_langid' );

			$table->addForeignKeyConstraint( 'mshop_locale_currency', array( 'currencyid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_msloc_currid' );

			return $schema;
		},
	),
);
