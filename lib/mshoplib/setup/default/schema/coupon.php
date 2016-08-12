<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(

		'mshop_coupon' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_coupon' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'provider', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'status', 'smallint', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscou_id' );
			$table->addIndex( array( 'siteid', 'status', 'start', 'end' ), 'idx_mscou_sid_stat_start_end' );
			$table->addIndex( array( 'siteid', 'provider' ), 'idx_mscou_sid_provider' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscou_sid_label' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mscou_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mscou_sid_end' );

			return $schema;
		},

		'mshop_coupon_code' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_coupon_code' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'count', 'integer', array() );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscouco_id' );
			$table->addUniqueIndex( array( 'siteid', 'code' ), 'unq_mscouco_sid_code' );
			$table->addIndex( array( 'siteid', 'count', 'start', 'end' ), 'idx_mscouco_sid_ct_start_end' );
			$table->addIndex( array( 'siteid', 'start' ), 'idx_mscouco_sid_start' );
			$table->addIndex( array( 'siteid', 'end' ), 'idx_mscouco_sid_end' );
			$table->addIndex( array( 'parentid' ), 'fk_mscouco_pid' );

			$table->addForeignKeyConstraint( 'mshop_coupon', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscouco_parentid' );

			return $schema;
		},
	)
);
