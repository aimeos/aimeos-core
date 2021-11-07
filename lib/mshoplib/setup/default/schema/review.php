<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


return array(
	'table' => array(
		'mshop_review' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msrev_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->refid();
			$table->string( 'customerid', 36 )->default( '' );
			$table->string( 'ordprodid', 36 )->default( '' );
			$table->string( 'name', 32 )->default( '' );
			$table->smallint( 'status' )->default( -1 );
			$table->smallint( 'rating' );
			$table->text( 'comment' )->default( '' );
			$table->text( 'response' )->default( '' );
			$table->meta();

			$table->unique( ['siteid', 'customerid', 'domain', 'refid'], 'unq_msrev_sid_cid_dom_rid' );
			$table->index( ['siteid', 'domain', 'refid', 'status', 'ctime'], 'idx_msrev_sid_dom_rid_sta_ct' );
			$table->index( ['siteid', 'domain', 'refid', 'status', 'rating'], 'idx_msrev_sid_dom_rid_sta_rate' );
			$table->index( ['siteid', 'domain', 'customerid', 'mtime'], 'idx_msrev_sid_dom_cid_mt' );
			$table->index( ['siteid', 'rating', 'domain'], 'idx_msrev_sid_rate_dom' );
		},
	),
);
