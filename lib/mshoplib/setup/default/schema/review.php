<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


return array(
	'table' => array(
		'mshop_review' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_review' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'bigint', ['autoincrement' => true] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'domain', 'string', ['length' => 32] );
			$table->addColumn( 'refid', 'string', ['length' => 36] );
			$table->addColumn( 'customerid', 'string', ['length' => 36] );
			$table->addColumn( 'ordprodid', 'string', ['length' => 36] );
			$table->addColumn( 'name', 'string', ['length' => 32] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'rating', 'smallint', [] );
			$table->addColumn( 'comment', 'text', ['length' => 0xffff] );
			$table->addColumn( 'response', 'text', ['length' => 0xffff] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', ['length' => 255] );

			$table->setPrimaryKey( ['id'], 'pk_msrev_id' );
			$table->addUniqueIndex( ['siteid', 'domain', 'refid', 'customerid'], 'unq_msrev_sid_dom_rid_cid' );
			$table->addIndex( ['siteid', 'domain', 'refid', 'status', 'ctime'], 'idx_msrev_sid_dom_rid_sta_ct' );
			$table->addIndex( ['siteid', 'customerid', 'domain', 'mtime'], 'idx_msrev_sid_cid_dom_mt' );
			$table->addIndex( ['siteid', 'rating'], 'idx_msrev_sid_rate' );
			$table->addIndex( ['siteid', 'mtime'], 'idx_msrev_sid_mt' );

			return $schema;
		},
	),
);
