<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2025
 */


return array(
	'table' => array(
		'mshop_basket' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'id' )->primary( 'pk_msbas_id' );
			$table->string( 'siteid' );
			$table->refid( 'customerid' )->default( '' );
			$table->text( 'content', 0x7fffff )->default( '' );
			$table->string( 'name' )->default( '' );
			$table->meta();

			$table->index( ['customerid'], 'idx_msbas_custid' );
			$table->index( ['mtime'], 'idx_msbas_mtime' );
		},
	),
);
