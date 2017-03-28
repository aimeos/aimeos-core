<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'mshop_tag_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_tag_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mstagty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mstagty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_mstagty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mstagty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mstagty_sid_code' );

			return $schema;
		},

		'mshop_tag' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_tag' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'typeid', 'integer', [] );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mstag_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'typeid', 'langid', 'label' ), 'unq_mstag_sid_dom_tid_lid_lab' );
			$table->addIndex( array( 'siteid', 'domain', 'langid' ), 'idx_mstag_sid_dom_langid' );
			$table->addIndex( array( 'siteid', 'domain', 'label' ), 'idx_mstag_sid_dom_label' );
			$table->addIndex( array( 'typeid' ), 'fk_mstag_typeid' );

			$table->addForeignKeyConstraint( 'mshop_tag_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mstag_typeid' );

			return $schema;
		},
	),
);
