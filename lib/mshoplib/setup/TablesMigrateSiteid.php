<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class TablesMigrateSiteid extends Base
{
	private $resources = [
		'db-attribute' => [
			'mshop_attribute_type', 'mshop_attribute_list_type', 'mshop_attribute_property_type',
			'mshop_attribute_list', 'mshop_attribute_property', 'mshop_attribute'
		],
		'db-cache' => [
			'madmin_cache_tag', 'madmin_cache'
		],
		'db-catalog' => [
			'mshop_catalog_list_type', 'mshop_catalog_list', 'mshop_catalog'
		],
		'db-coupon' => [
			'mshop_coupon_code', 'mshop_coupon'
		],
		'db-customer' => [
			'mshop_customer_list_type', 'mshop_customer_property_type', 'mshop_customer_group',
			'mshop_customer_property', 'mshop_customer_list', 'mshop_customer_address', 'mshop_customer',
		],
		'db-job' => [
			'madmin_job',
		],
		'db-locale' => [
			'mshop_locale_site', 'mshop_locale',
		],
		'db-log' => [
			'madmin_log',
		],
		'db-media' => [
			'mshop_media_type', 'mshop_media_list_type', 'mshop_media_property_type',
			'mshop_media_list', 'mshop_media_property', 'mshop_media'
		],
		'db-order' => [
			'mshop_order_base_product_attr', 'mshop_order_base_service_attr', 'mshop_order_base_coupon',
			'mshop_order_base_product', 'mshop_order_base_service', 'mshop_order_base_address',
			'mshop_order_base', 'mshop_order_status', 'mshop_order'
		],
		'db-plugin' => [
			'mshop_plugin_type', 'mshop_plugin'
		],
		'db-price' => [
			'mshop_price_type', 'mshop_price_list_type', 'mshop_price_list', 'mshop_price'
		],
		'db-product' => [
			'mshop_index_attribute', 'mshop_index_catalog', 'mshop_index_price', 'mshop_index_supplier', 'mshop_index_text',
			'mshop_product_list_type', 'mshop_product_property_type', 'mshop_product_type',
			'mshop_product_list', 'mshop_product_property', 'mshop_product'
		],
		'db-service' => [
			'mshop_service_type', 'mshop_service_list_type', 'mshop_service_list', 'mshop_service'
		],
		'db-stock' => [
			'mshop_stock_type', 'mshop_stock'
		],
		'db-subscription' => [
			'mshop_subscription'
		],
		'db-supplier' => [
			'mshop_supplier_list_type', 'mshop_supplier_list', 'mshop_supplier_address', 'mshop_supplier'
		],
		'db-tag' => [
			'mshop_tag_type', 'mshop_tag'
		],
		'db-text' => [
			'mshop_text_type', 'mshop_text_list_type', 'mshop_text_list', 'mshop_text'
		],
	];


	public function before() : array
	{
		return [
			'Attribute', 'Cache', 'Catalog', 'Coupon', 'Customer', 'Job', 'Locale', 'Log', 'Media', 'Order',
			'Plugin', 'Price', 'Product', 'Service', 'Stock', 'Subscription', 'Supplier', 'Tag', 'Text'
		];
	}


	public function up()
	{
		$db = $this->db( 'db-locale' );

		if( !$db->hasTable( 'mshop_locale_site' ) || $db->hasColumn( 'mshop_locale_site', 'siteid' ) ) {
			return;
		}

		$this->info( 'Update "siteid" columns', 'v' );

		$db->table( 'mshop_locale_site' )->int( 'siteid' )->default( 0 )->up();

		$db->dropForeign( 'mshop_locale', ['fk_msloc_siteid', 'fk_msloccu_siteid', 'fk_mslocla_siteid'] );
		$db->dropForeign( 'mshop_locale_site', 'mshop_locale_site_siteid_key' ); // PostgreSQL workaround

		$db->exec( 'UPDATE mshop_locale_site SET siteid = id' );

		$this->process( $this->resources );
	}


	protected function process( array $resources )
	{
		$sites = $this->getSites();

		foreach( $resources as $rname => $tables )
		{
			$db = $this->db( $rname );

			foreach( $tables as $table )
			{
				$this->info( sprintf( 'Checking table %1$s', $table ), 'vv', 1 );
				$colname = null;

				if( $db->hasColumn( $table, 'siteid' ) && $db->table( $table )->col( 'siteid' )->type() === 'integer' ) {
					$colname = 'siteid';
				}

				if( $db->hasColumn( $table, 'tsiteid' ) && $db->table( $table )->col( 'tsiteid' )->type() === 'integer' )
				{
					$db->dropForeign( 'madmin_cache_tag', ['fk_macac_tid_tsid'] );
					$colname = 'tsiteid';
				}

				if( $colname )
				{
					$db->table( $table )->string( $colname )->up();

					foreach( $sites as $siteid => $site )
					{
						$db->stmt()->update( $table )->set( $colname, '?' )
							->where( $colname . ' = ?' )->orWhere( $colname . " = ''" )
							->setParameters( [$site, $siteid] )
							->execute();
					}
				}
			}
		}
	}


	protected function getSites()
	{
		$map = [];

		$dbm = $this->context()->getDatabaseManager();
		$conn = $dbm->acquire( 'db-locale' );
		$tconn = $dbm->acquire( 'db-locale' );

		$type = \Aimeos\MW\DB\Statement\Base::PARAM_INT;
		$roots = $conn->create( 'SELECT id, nleft, nright FROM mshop_locale_site WHERE level = 0' )->execute();

		while( $root = $roots->fetch() )
		{
			$sql = 'SELECT id, nleft, nright FROM mshop_locale_site WHERE nleft >= ? and nright <= ? ORDER BY nleft';
			$result = $tconn->create( $sql )->bind( 1, $root['nleft'], $type )->bind( 2, $root['nright'], $type )->execute();

			while( $row = $result->fetch() )
			{
				$map[$row['id']] = $row['id'] . '.';
				$this->map( $result, $row, $map, $row['id'] . '.' );
			}
		}

		$dbm->release( $tconn, 'db-locale' );
		$dbm->release( $conn, 'db-locale' );

		return $map;
	}


	protected function isChild( array $row, array $parent )
	{
		return $row['nleft'] > $parent['nleft'] && $row['nright'] < $parent['nright'];
	}


	protected function map( \Aimeos\MW\DB\Result\Iface $result, array $parent, array &$map, string $site )
	{
		while( $row = $result->fetch() )
		{
			while( $this->isChild( $row, $parent ) )
			{
				$map[$row['id']] = $site . $row['id'] . '.';

				if( ( $row = $this->map( $result, $row, $map, $site . $row['id'] . '.' ) ) === null ) {
					return null;
				}
			}

			return $row;
		}

		return null;
	}
}
