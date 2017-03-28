<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'madmin_log' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_log' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
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
