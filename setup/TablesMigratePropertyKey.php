<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class TablesMigratePropertyKey extends Base
{
	protected function tables()
	{
		return [
			'db-attribute' => 'mshop_attribute_property',
			'db-customer' => 'mshop_customer_property',
			'db-media' => 'mshop_media_property',
			'db-price' => 'mshop_price_property',
			'db-product' => 'mshop_product_property',
		];
	}


	public function after() : array
	{
		return ['TypesMigrateColumns', 'TablesClearPropertyKey', 'Attribute', 'Customer', 'Media', 'Price', 'Product'];
	}


	public function up()
	{
		$this->info( 'Update property "key" columns', 'vv' );

		foreach( $this->tables() as $rname => $table )
		{
			$this->info( sprintf( 'Checking table %1$s', $table ), 'vv', 1 );

			$db = $this->db( $rname );

			if( $db->hasTable( $table ) )
			{
				$db2 = $this->db( $rname, true );

				$update = $db2->stmt()->update( $table )->set( $db2->qi( 'key' ), '?' )->where( $db2->qi( 'id' ) . '= ?' );

				$q = $db->stmt();
				$result = $q->select( 'id', 'type', 'langid', 'value' )->from( $table )
					->where( $db->qi( 'key' ) . ' = \'\'' )->execute();

				while( $row = $result->fetch() )
				{
					$value = substr( $row['type'] . '|' . ( $row['langid'] ?: 'null' ) . '|' . $row['value'], 0, 255 );
					$update->setParameters( [$value, $row['id']] )->execute();
				}

				$db2->close();
			}
		}
	}
}
