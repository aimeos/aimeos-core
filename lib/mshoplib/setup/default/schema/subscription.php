<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


return array(

	'table' => array(

		'mshop_subscription' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_subscription' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'ordprodid', 'bigint', [] );
			$table->addColumn( 'next', 'datetime', [] );
			$table->addColumn( 'end', 'datetime', ['notnull' => false] );
			$table->addColumn( 'interval', 'string', array( 'length' => 16 ) );
			$table->addColumn( 'status', 'smallint', array( 'default' => 0 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssub_id' );
			$table->addIndex( array( 'siteid', 'next', 'status' ), 'idx_mssub_sid_next_stat' );

			return $schema;
		},
	),
);