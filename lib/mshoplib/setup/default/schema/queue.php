<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'madmin_queue' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_queue' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'queue', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'cname', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'rtime', 'datetime', [] );
			$table->addColumn( 'message', 'text', array( 'length' => 0xffff ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_maque_id' );
			$table->addIndex( array( 'queue', 'rtime', 'cname' ), 'idx_maque_queue_rtime_cname' );

			return $schema;
		},
	),
);
