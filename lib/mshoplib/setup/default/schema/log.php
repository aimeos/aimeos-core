<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'madmin_log' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_log' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255, 'default' => ''] );
			$table->addColumn( 'facility', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'timestamp', 'datetime', [] );
			$table->addColumn( 'priority', 'smallint', [] );
			$table->addColumn( 'message', 'text', array( 'length' => 0x1ffff ) );
			$table->addColumn( 'request', 'string', array( 'length' => 32 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslog_id' );
			$table->addIndex( array( 'siteid', 'timestamp', 'facility', 'priority' ), 'idx_malog_sid_time_facility_prio' );

			return $schema;
		},
	),
);
