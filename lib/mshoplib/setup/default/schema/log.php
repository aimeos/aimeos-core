<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_malog_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_malog_id' );

			return $schema;
		}
	),
	'table' => array(
		'madmin_log' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_log' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'facility', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'timestamp', 'datetime', array() );
			$table->addColumn( 'priority', 'smallint', array() );
			$table->addColumn( 'message', 'text', array( 'length' => 0x1ffff ) );
			$table->addColumn( 'request', 'string', array( 'length' => 32 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mslog_id' );
			$table->addIndex( array( 'siteid', 'timestamp', 'facility', 'priority' ), 'idx_malog_sid_time_facility_prio' );

			return $schema;
		},
	),
);
