<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(
	'table' => array(
		'madmin_cache' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_cache' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'expire', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0x1ffff ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_macac_id' );
			$table->addIndex( array( 'expire' ), 'idx_majob_expire' );

			return $schema;
		},

		'madmin_cache_tag' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_cache_tag' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'tid', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'tname', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'tid', 'tname' ), 'unq_macacta_tid_tname' );
			$table->addIndex( array( 'tid' ), 'fk_macac_tid' );

			$table->addForeignKeyConstraint( 'madmin_cache', array( 'tid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_macacta_tid' );

			return $schema;
		},
	),
);
