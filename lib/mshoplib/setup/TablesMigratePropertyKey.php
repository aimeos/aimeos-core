<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class TablesMigratePropertyKey extends Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_property',
		'db-customer' => 'mshop_customer_property',
		'db-media' => 'mshop_media_property',
		'db-price' => 'mshop_price_property',
		'db-product' => 'mshop_product_property',
	];


	public function after() : array
	{
		return ['TypesMigrateColumns', 'TablesClearPropertyKey', 'Attribute', 'Customer', 'Media', 'Price', 'Product'];
	}


	public function up()
	{
		$this->info( 'Update property "key" columns', 'v' );

		foreach( $this->tables as $rname => $table )
		{
			$this->info( sprintf( 'Checking table %1$s', $table ), 'vv', 1 );

			$db = $this->db( $rname );
			$db2 = $this->db( $rname, true );

			$update = $db->stmt()->update( $table )->set( $db->qi( 'key' ), '?' )->where( 'id', '?' );

			$q = $db->stmt();
			$result = $q->select( 'id', 'type', 'langid', 'value' )->from( $table )
				->where( $db->qi( 'key' ) . ' = \'\'' )->execute();

			while( $row = $result->fetch() ) {
				$update->setParameters( [$row['type'] . '|' . ( $row['langid'] ?: 'null' ) . '|' . md5( $row['value'] ), $row['id']] )->execute();
			}

			$db2->close();
		}
	}
}
