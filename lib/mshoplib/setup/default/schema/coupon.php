<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 */


return array(
	'table' => array(

		'mshop_coupon' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_coupon' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'provider', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscou_id' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_mscou_sid_stat_start_end' );
			$table->addIndex( array( 'siteid', 'provider' ), 'idx_mscou_sid_provider' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscou_sid_label' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mscou_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mscou_sid_end' );

			return $schema;
		},

		'mshop_coupon_code' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_coupon_code' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'count', 'integer', ['notnull' => false, 'default' => 0] );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'ref', 'string', array( 'length' => 36, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscouco_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mscouco_sid_code' );
			$table->addIndex( array( 'siteid', 'count', 'start', 'end' ), 'idx_mscouco_sid_ct_start_end' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mscouco_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mscouco_sid_end' );
			$table->addIndex( array( 'parentid' ), 'fk_mscouco_pid' );

			$table->addForeignKeyConstraint( 'mshop_coupon', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscouco_pid' );

			return $schema;
		},
	)
);
