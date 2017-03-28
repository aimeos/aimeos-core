<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(

	'exclude' => array(
		'idx_msindte_p_s_lt_la_ty_do_va',
		'idx_msindte_value',
	),


	'table' => array(

		'mshop_index_attribute' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_attribute' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'attrid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

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
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'catid', 'listtype', 'pos' ), 'unq_msindca_p_s_cid_lt_po' );
			$table->addIndex( array( 'siteid', 'catid', 'listtype', 'pos' ), 'idx_msindca_s_ca_lt_po' );

			return $schema;
		},

		'mshop_index_price' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_price' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'priceid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3, 'fixed' => true ) );
			$table->addColumn( 'value', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 5, 'scale' => 2 ) );
			$table->addColumn( 'quantity', 'integer', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'priceid', 'listtype' ), 'unq_msindpr_p_s_prid_lt' );
			$table->addIndex( array( 'siteid', 'listtype', 'currencyid', 'type', 'value' ), 'idx_msindpr_s_lt_cu_ty_va' );
			$table->addIndex( array( 'prodid', 'siteid', 'listtype', 'currencyid', 'type', 'value' ), 'idx_msindpr_p_s_lt_cu_ty_va' );

			return $schema;
		},

		'mshop_index_text' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_text' );
			$table->addOption( 'engine', 'MyISAM' );

			$table->addColumn( 'prodid', 'integer', [] );
			$table->addColumn( 'siteid', 'integer', [] );
			$table->addColumn( 'textid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false  ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'textid', 'listtype' ), 'unq_msindte_p_s_tid_lt' );

			return $schema;
		},
	),
);
