<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 */


return array(

	'exclude' => array(
		'idx_msindte_content',
	),


	'table' => array(

		'mshop_index_attribute' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_attribute' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'attrid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'attrid', 'listtype' ), 'unq_msindat_p_s_aid_lt' );
			$table->addIndex( array( 'prodid', 'siteid', 'listtype', 'type', 'code' ), 'idx_msindat_p_s_lt_t_c' );
			$table->addIndex( array( 'siteid', 'attrid', 'listtype' ), 'idx_msindat_s_at_lt' );

			return $schema;
		},

		'mshop_index_catalog' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_catalog' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'catid', 'integer', [] );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'catid', 'listtype', 'pos' ), 'unq_msindca_p_s_cid_lt_po' );
			$table->addIndex( array( 'siteid', 'catid', 'listtype', 'pos' ), 'idx_msindca_s_ca_lt_po' );

			return $schema;
		},

		'mshop_index_price' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_price' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'value', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'currencyid' ), 'unq_msindpr_pid_sid_cid' );
			$table->addIndex( array( 'siteid', 'currencyid', 'value' ), 'idx_msindpr_sid_cid_val' );

			return $schema;
		},

		'mshop_index_supplier' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_supplier' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'supid', 'integer', [] );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'supid', 'listtype', 'pos' ), 'unq_msindsup_p_sid_supid_lt_po' );
			$table->addIndex( array( 'siteid', 'supid', 'listtype', 'pos' ), 'idx_msindsup_sid_supid_lt_po' );

			return $schema;
		},

		'mshop_index_text' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_text' );
			$table->addOption( 'engine', 'MyISAM' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'langid', 'string', ['length' => 5, 'notnull' => false] );
			$table->addColumn( 'url', 'string', ['length' => 240] );
			$table->addColumn( 'name', 'string', ['length' => 240] );
			$table->addColumn( 'content', 'text', ['length' => 0xffffff] );
			$table->addColumn( 'mtime', 'datetime', [] );

			$table->addUniqueIndex( ['prodid', 'siteid', 'langid', 'url'], 'unq_msindte_pid_sid_lid_url' );
			$table->addIndex( ['prodid', 'siteid', 'langid', 'name'], 'idx_msindte_pid_sid_lid_name' );

			return $schema;
		},
	),
);
