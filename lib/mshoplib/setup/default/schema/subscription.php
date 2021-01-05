<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


return array(

	'table' => array(

		'mshop_subscription' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_subscription' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'ordprodid', 'bigint', [] );
			$table->addColumn( 'next', 'date', ['notnull' => false] );
			$table->addColumn( 'end', 'date', ['notnull' => false] );
			$table->addColumn( 'productid', 'string', array( 'length' => 36, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'interval', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'reason', 'smallint', array( 'notnull' => false ) );
			$table->addColumn( 'period', 'smallint', array( 'default' => 0 ) );
			$table->addColumn( 'status', 'smallint', array( 'default' => 0 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssub_id' );
			$table->addIndex( array( 'siteid', 'next', 'status' ), 'idx_mssub_sid_next_stat' );
			$table->addIndex( array( 'siteid', 'baseid' ), 'idx_mssub_sid_baseid' );
			$table->addIndex( array( 'siteid', 'ordprodid' ), 'idx_mssub_sid_opid' );
			$table->addIndex( array( 'siteid', 'productid', 'period' ), 'idx_mssub_sid_pid_period' );

			return $schema;
		},
	),
);
