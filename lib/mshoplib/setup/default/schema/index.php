<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


return array(
	'table' => array(

		'mshop_index_attribute' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_attribute' );

			$table->addColumn( 'prodid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'attrid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 32, 'collation' => 'utf8_bin' ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'attrid', 'listtype' ), 'unq_msindat_p_s_aid_lt' );
			$table->addIndex( array( 'prodid', 'siteid', 'listtype', 'type', 'code' ), 'idx_msindat_p_s_lt_t_c' );
			$table->addIndex( array( 'siteid', 'attrid', 'listtype' ), 'idx_msindat_s_at_lt' );

			return $schema;
		},

		'mshop_index_catalog' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_catalog' );

			$table->addColumn( 'prodid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'catid', 'integer', array() );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'pos', 'integer', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'catid', 'listtype', 'pos' ), 'unq_msindca_p_s_cid_lt_po' );
			$table->addIndex( array( 'siteid', 'catid', 'listtype', 'pos' ), 'idx_msindca_s_ca_lt_po' );

			return $schema;
		},

		'mshop_index_price' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_price' );

			$table->addColumn( 'prodid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'priceid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'currencyid', 'string', array( 'length' => 3 ) );
			$table->addColumn( 'value', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'costs', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'rebate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'taxrate', 'decimal', array( 'precision' => 12, 'scale' => 2 ) );
			$table->addColumn( 'quantity', 'integer', array() );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'priceid', 'listtype' ), 'unq_msindpr_p_s_prid_lt' );
			$table->addIndex( array( 'siteid', 'listtype', 'currencyid', 'type', 'value' ), 'idx_msindpr_s_lt_cu_ty_va' );
			$table->addIndex( array( 'prodid', 'siteid', 'listtype', 'currencyid', 'type', 'value' ), 'idx_msindpr_p_s_lt_cu_ty_va' );

			return $schema;
		},

		'mshop_index_text' => function ( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_index_text' );
			$table->addOption( 'engine', 'MyISAM' );

			$table->addColumn( 'prodid', 'integer', array() );
			$table->addColumn( 'siteid', 'integer', array() );
			$table->addColumn( 'textid', 'integer', array( 'notnull' => false ) );
			$table->addColumn( 'listtype', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'type', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'langid', 'string', array( 'length' => 5, 'notnull' => false  ) );
			$table->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'mtime', 'datetime', array() );
			$table->addColumn( 'ctime', 'datetime', array() );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->addUniqueIndex( array( 'prodid', 'siteid', 'textid', 'listtype' ), 'unq_msindte_p_s_tid_lt' );
			$table->addIndex( array( 'value' ), 'idx_msindte_value', array( 'fulltext' ) );
			$table->addIndex( array( 'siteid' ), 'idx_msindte_sid' );

			return $schema;
		},
	),
);
