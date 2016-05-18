<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'sequence' => array(
		'seq_majob_id' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$schema->createSequence( 'seq_majob_id' );

			return $schema;
		}
	),
	'table' => array(
		'madmin_job' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_job' );

			$table->addColumn( 'id', 'bigint', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'method', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'parameter', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'result', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_majob_id' );
			$table->addIndex( array( 'ctime' ), 'idx_majob_ctime' );
			$table->addIndex( array( 'siteid', 'status' ), 'idx_majob_sid_status' );

			return $schema;
		},
	),
);