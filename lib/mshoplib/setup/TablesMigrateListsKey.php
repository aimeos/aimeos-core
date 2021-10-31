<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class TablesMigrateListsKey extends Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_list',
		'db-catalog' => 'mshop_catalog_list',
		'db-customer' => 'mshop_customer_list',
		'db-media' => 'mshop_media_list',
		'db-price' => 'mshop_price_list',
		'db-product' => 'mshop_product_list',
		'db-service' => 'mshop_service_list',
		'db-supplier' => 'mshop_supplier_list',
		'db-text' => 'mshop_text_list',
	];


	public function after() : array
	{
		return ['TypesMigrateColumns', 'Attribute', 'Catalog', 'Customer', 'Media', 'Price', 'Product', 'Service', 'Supplier', 'Text'];
	}


	public function up()
	{
		$this->info( 'Update lists "key" columns', 'v' );

		$this->process( $this->tables );
	}


	protected function process( $tables )
	{
		foreach( $tables as $rname => $table )
		{
			$this->info( sprintf( 'Checking table %1$s', $table ), 'vv', 1 );

			$db = $this->db( $rname );
			$db2 = $this->db( $rname, true );

			$update = $db->stmt()->update( $table )->set( $db->qi( 'key' ), '?' )->where( 'id', '?' );

			$q = $db->stmt();
			$result = $q->select( 'id', 'domain', 'type', 'refid' )->from( $table )
				->where( $db->qi( 'key' ) . ' = \'\'' )->execute();

			while( $row = $result->fetch() ) {
				$update->setParameters( [$row['domain'] . '|' . $row['type'] . '|' . $row['refid'], $row['id']] )->execute();
			}

			$db2->close();
		}
	}
}
