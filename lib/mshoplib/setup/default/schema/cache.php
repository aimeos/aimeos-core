<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(
		'madmin_cache' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_cache' );

			$table->addColumn( 'id', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'expire', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0x1ffff ) );

			$table->addUniqueIndex( array( 'id', 'siteid' ), 'unq_macac_id_siteid' );
			$table->addIndex( array( 'expire' ), 'idx_majob_expire' );

			return $schema;
		},

		'madmin_cache_tag' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'madmin_cache_tag' );

			$table->addColumn( 'tid', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'tsiteid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'tname', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'tid', 'tsiteid', 'tname' ), 'unq_macacta_tid_tsid_tname' );
			$table->addIndex( array( 'tid', 'tsiteid' ), 'fk_macac_tid_tsid' );

			$table->addForeignKeyConstraint( 'madmin_cache', array( 'tid', 'tsiteid' ), array( 'id', 'siteid' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_macac_tid_tsid' );

			return $schema;
		},
	),
);
