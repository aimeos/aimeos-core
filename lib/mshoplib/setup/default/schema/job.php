<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'madmin_job' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_job' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'method', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'parameter', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'result', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_majob_id' );
			$table->addIndex( array( 'siteid', 'ctime' ), 'idx_majob_sid_ctime' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_majob_sid_status' );

			return $schema;
		},
	),
);