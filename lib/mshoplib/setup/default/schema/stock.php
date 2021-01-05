<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'mshop_stock_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_stock_type' );
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

			$table->setPrimaryKey( array( 'id' ), 'pk_msstoty_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_msstoty_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_msstoty_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_msstoty_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_msstoty_sid_code' );

			return $schema;
		},

		'mshop_stock' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_stock' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'prodid', 'string', array( 'length' => 36, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'stocklevel', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'backdate', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'timeframe', 'string', array( 'length' => 16, 'default' => '' ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssto_id' );
			$table->addUniqueIndex( array( 'siteid', 'prodid', 'type' ), 'unq_mssto_sid_pid_ty' );
			$table->addIndex( array( 'siteid', 'stocklevel' ), 'idx_mssto_sid_stocklevel' );
			$table->addIndex( array( 'siteid', 'backdate' ), 'idx_mssto_sid_backdate' );

			return $schema;
		},
	),
);
