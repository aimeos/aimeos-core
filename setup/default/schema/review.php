<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


return array(
	'table' => array(
		'mshop_review' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->bigid()->primary( 'pk_msrev_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->refid();
			$table->string( 'customerid', 36 )->null( true );
			$table->string( 'ordprodid', 36 )->default( '' );
			$table->string( 'name', 32 )->default( '' );
			$table->smallint( 'status' )->default( -1 );
			$table->smallint( 'rating' );
			$table->text( 'comment' )->default( '' );
			$table->text( 'response' )->default( '' );
			$table->meta();

			$table->unique( ['customerid', 'domain', 'refid', 'siteid'], 'unq_msrev_cid_dom_rid_sid' );
			$table->index( ['domain', 'refid', 'status', 'ctime', 'siteid'], 'idx_msrev_dom_rid_sta_ct_sid' );
			$table->index( ['domain', 'refid', 'status', 'rating', 'siteid'], 'idx_msrev_dom_rid_sta_rate_sid' );
			$table->index( ['domain', 'customerid', 'mtime', 'siteid'], 'idx_msrev_dom_cid_mt_sid' );
			$table->index( ['rating', 'domain', 'siteid'], 'idx_msrev_rate_dom_sid' );
		},
	),
);
