<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'mshop_stock_type' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_stock_type' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_msstoty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msstoty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_msstoty_sid_status' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msstoty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msstoty_sid_code' );

			return $schema;
		},

		'mshop_stock' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_stock' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'typeid', 'integer', [] );
			$table->addColumn( 'productcode', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'stocklevel', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'backdate', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssto_id' );
			$table->addUniqueIndex( array( 'siteid', 'productcode', 'typeid' ), 'unq_mssto_sid_pcode_tid' );
			$table->addIndex( array( 'siteid', 'stocklevel' ), 'idx_mssto_sid_stocklevel' );
			$table->addIndex( array( 'siteid', 'backdate' ), 'idx_mssto_sid_backdate' );
			$table->addIndex( array( 'typeid' ), 'fk_mssto_typeid' );

			$table->addForeignKeyConstraint( 'mshop_stock_type', array( 'typeid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mssto_typeid' );

			return $schema;
		},
	),
);
