<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


class TablesClearPropertyKey extends Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_property',
		'db-customer' => 'mshop_customer_property',
		'db-media' => 'mshop_media_property',
		'db-price' => 'mshop_price_property',
		'db-product' => 'mshop_product_property',
	];


	public function before() : array
	{
		return ['Attribute', 'Customer', 'Media', 'Price', 'Product'];
	}


	public function up()
	{
		$this->info( 'Clear property "key" columns', 'v' );

		foreach( $this->tables as $rname => $table )
		{
			$db = $this->db( $rname );

			if( $db->hasColumn( $table, 'key' ) && $db->table( $table )->col( 'key' )->length() !== 103 )
			{
				$this->info( sprintf( 'Updateing table %1$s', $table ), 'vv', 1 );
				$db->exec( sprintf( 'UPDATE %1$s SET key = \'\'', $table ) );
			}
		}
	}
}
