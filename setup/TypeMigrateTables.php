<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\Upscheme\Task;


class TypeMigrateTables extends Base
{
	public function after() : array
	{
		return ['Type'];
	}


	public function before() : array
	{
		return ['Attribute', 'Catalog', 'Customer', 'Media', 'Plugin', 'Price', 'Product', 'Rule', 'Service', 'Supplier', 'Stock', 'Tag', 'Text'];
	}


	public function up()
	{
		$this->info( 'Migrate types to mshop_type table', 'vv' );

		$map = [
			'attribute' => ['mshop_attribute_type', 'mshop_attribute_list_type', 'mshop_attribute_property_type'],
			'catalog' => ['mshop_catalog_list_type'],
			'customer' => ['mshop_customer_list_type', 'mshop_customer_property_type'],
			'media' => ['mshop_media_type', 'mshop_media_list_type', 'mshop_media_property_type'],
			'plugin' => ['mshop_plugin_type'],
			'price' => ['mshop_price_type', 'mshop_price_list_type', 'mshop_price_property_type'],
			'product' => ['mshop_product_type', 'mshop_product_list_type', 'mshop_product_property_type'],
			'rule' => ['mshop_rule_type'],
			'service' => ['mshop_service_type', 'mshop_service_list_type'],
			'supplier' => ['mshop_supplier_list_type'],
			'stock' => ['mshop_stock_type'],
			'tag' => ['mshop_tag_type'],
			'text' => ['mshop_text_type', 'mshop_text_list_type'],
		];

		$db = $this->db( 'db-type' );

		foreach( $map as $domain => $tables )
		{
			$db2 = $this->db( 'db-' . $domain );

			foreach( $tables as $table )
			{
				if( !$db2->hasTable( $table ) ) {
					continue;
				}

				$for = $domain;

				if( strpos( $table, '_list_' ) !== false ) {
					$for .= '/lists';
				}

				if( strpos( $table, '_property_' ) !== false ) {
					$for .= '/property';
				}

				$result = $db2->query( 'SELECT * FROM ' . $db2->qi( $table ) );

				while( $row = $result->fetchAssociative() )
				{
					try
					{
						unset( $row['id'] );
						$db->insert( 'mshop_type', $row + ['for' => $for] );
					}
					catch( \Exception $e ) {} // Ignore duplicate entries
				}
			}
		}
	}
}
