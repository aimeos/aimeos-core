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
			$table->addColumn( 'baseid', 'bigint', [] );
			$table->addColumn( 'ordprodid', 'bigint', [] );
			$table->addColumn( 'next', 'date', ['notnull' => false] );
			$table->addColumn( 'end', 'date', ['notnull' => false] );
			$table->addColumn( 'interval', 'string', array( 'length' => 16 ) );
			$table->addColumn( 'reason', 'smallint', array( 'notnull' => false ) );
			$table->addColumn( 'status', 'smallint', array( 'default' => 0 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array('length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mssub_id' );
			$table->addIndex( array( 'siteid', 'next', 'status' ), 'idx_mssub_sid_next_stat' );
			$table->addIndex( array( 'siteid', 'baseid' ), 'idx_mssub_sid_baseid' );
			$table->addIndex( array( 'siteid', 'ordprodid' ), 'idx_mssub_sid_opid' );

			return $schema;
		},
	),
);