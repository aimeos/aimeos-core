<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


return array(

	'exclude' => array(
		'idx_msindte_content',
	),


	'table' => array(

		'mshop_index_attribute' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_attribute' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'artid', 'integer', ['notnull' => false] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'attrid', 'string', ['length' => 36, 'customSchemaOptions' => ['charset' => 'binary']] );
			$table->addColumn( 'listtype', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'attrid', 'listtype' ), 'unq_msindat_p_s_aid_lt' );
			$table->addIndex( array( 'prodid', 'siteid', 'listtype', 'type', 'code' ), 'idx_msindat_p_s_lt_t_c' );
			$table->addIndex( array( 'siteid', 'attrid', 'listtype' ), 'idx_msindat_s_at_lt' );

			return $schema;
		},

		'mshop_index_catalog' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_catalog' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'catid', 'string', ['length' => 36, 'customSchemaOptions' => ['charset' => 'binary']] );
			$table->addColumn( 'listtype', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'catid', 'listtype', 'pos' ), 'unq_msindca_p_s_cid_lt_po' );
			$table->addIndex( array( 'siteid', 'catid', 'listtype', 'pos' ), 'idx_msindca_s_ca_lt_po' );

			return $schema;
		},

		'mshop_index_price' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_price' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'value', 'decimal', array( 'precision' => 12, 'scale' => 2, 'notnull' => false ) );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'currencyid' ), 'unq_msindpr_pid_sid_cid' );
			$table->addIndex( array( 'siteid', 'currencyid', 'value' ), 'idx_msindpr_sid_cid_val' );

			return $schema;
		},

		'mshop_index_supplier' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_supplier' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'supid', 'string', ['length' => 36, 'customSchemaOptions' => ['charset' => 'binary']] );
			$table->addColumn( 'listtype', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'latitude', 'float', ['notnull' => false] );
			$table->addColumn( 'longitude', 'float', ['notnull' => false] );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'supid', 'listtype', 'pos' ), 'unq_msindsup_p_sid_supid_lt_po' );
			$table->addIndex( array( 'prodid', 'latitude', 'longitude', 'siteid' ), 'idx_msindsup_p_lat_lon_sid' );
			$table->addIndex( array( 'siteid', 'supid', 'listtype', 'pos' ), 'idx_msindsup_sid_supid_lt_po' );

			return $schema;
		},

		'mshop_index_text' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_text' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', ['autoincrement' => true] );
			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'langid', 'string', ['length' => 5, 'notnull' => false] );
			$table->addColumn( 'url', 'string', ['length' => 255] );
			$table->addColumn( 'name', 'string', ['length' => 255] );
			$table->addColumn( 'content', 'text', ['length' => 0xffffff] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->setPrimaryKey( ['id'], 'pk_msindte_id' );
			$table->addUniqueIndex( ['prodid', 'siteid', 'langid', 'url'], 'unq_msindte_pid_sid_lid_url' );
			$table->addIndex( ['prodid', 'siteid', 'langid', 'name'], 'idx_msindte_pid_sid_lid_name' );

			return $schema;
		},
	),
);
