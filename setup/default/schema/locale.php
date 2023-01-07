<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


return array(
	'table' => array(

		'mshop_locale_currency' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'id', 3 )->primary( 'pk_msloccu_id' );
			$table->string( 'label' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['status'], 'idx_msloccu_status' );
			$table->index( ['label'], 'idx_msloccu_label' );
		},

		'mshop_locale_language' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->string( 'id', 5 )->primary( 'pk_mslocla_id' );
			$table->string( 'label' )->default( '' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->index( ['status'], 'idx_mslocla_status' );
			$table->index( ['label'], 'idx_mslocla_label' );
		},

		'mshop_locale_site' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_mslocsi_id' );
			$table->int( 'parentid' )->null( true );
			$table->string( 'siteid' )->default( '' );
			$table->code()->length( 255 )->default( '' );
			$table->string( 'label' )->default( '' );
			$table->string( 'icon' )->default( '' );
			$table->string( 'logo' )->default( '{}' );
			$table->config();
			$table->refid( 'refid' )->default( '' );
			$table->string( 'theme', 32 )->null( true );
			$table->smallint( 'level' )->default( 0 );
			$table->decimal( 'rating', 4 )->default( 0 );
			$table->int( 'ratings' )->default( 0 );
			$table->int( 'invoiceno' )->default( 1 );
			$table->int( 'nleft' );
			$table->int( 'nright' );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['code'], 'unq_mslocsi_code' );
			$table->unique( ['siteid'], 'unq_mslocsi_siteid' );
			$table->index( ['nleft', 'nright', 'level', 'parentid'], 'idx_mslocsi_nlt_nrt_lvl_pid' );
			$table->index( ['level', 'status'], 'idx_mslocsi_level_status' );
			$table->index( ['rating'], 'idx_mslocsi_rating' );
			$table->index( ['label'], 'idx_mslocsi_label' );
		},

		'mshop_locale' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id()->primary( 'pk_msloc_id' );
			$table->int( 'site_id' );
			$table->string( 'siteid' );
			$table->string( 'langid', 5 );
			$table->string( 'currencyid', 3 );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' )->default( 1 );
			$table->meta();

			$table->unique( ['langid', 'currencyid', 'site_id'], 'unq_msloc_lang_curr_sid' );

			$table->foreign( 'site_id', 'mshop_locale_site', 'id', 'fk_msloc_siteid' );
			$table->foreign( 'langid', 'mshop_locale_language', 'id', 'fk_msloc_langid' );
			$table->foreign( 'currencyid', 'mshop_locale_currency', 'id', 'fk_msloc_currid' );
		},
	),
);
