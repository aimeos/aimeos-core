<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_mslocsi_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_mslocsi_id' );
			return $schema;
		},
		'seq_msloc_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_msloc_id' );
			return $schema;
		},
	),
	'table' => array(

		'mshop_locale_site' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_site' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'customSchemaOptions' => array( 'collation' => 'utf8_bin' ) ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'level', 'smallint', array() );
			$table->addColumn( 'nleft', 'integer', array() );
			$table->addColumn( 'nright', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
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
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslocla_id' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mslocla_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mslocla_label' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL' ), 'fk_mslocla_siteid' );

			return $schema;
		},

		'mshop_locale_currency' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale_currency' );

			$table->addColumn( 'id', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msloccu_id' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msloccu_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mslocla_label' );

			$table->addForeignKeyConstraint( 'mshop_locale_site', array( 'siteid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL' ), 'fk_msloccu_siteid' );

			return $schema;
		},

		'mshop_locale' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_locale' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'langid', 'string', array( 'length' => 5 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msloc_id' );
			$table->addUniqueIndex( array( 'siteid', 'langid', 'currencyid' ), 'unq_msloc_sid_lang_curr' );
			$table->addIndex( array( 'siteid', 'currencyid' ), 'idx_msloc_sid_curid' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msloc_sid_status' );
			$table->addIndex( array( 'siteid', 'pos' ), 'idx_msloc_sid_pos' );

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
