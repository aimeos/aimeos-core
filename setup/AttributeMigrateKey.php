<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class AttributeMigrateKey extends Base
{
	public function before() : array
	{
		return ['Attribute'];
	}


	public function after() : array
	{
		return ['TypesMigrateColumns'];
	}


	public function up()
	{
		$db = $this->db( 'db-attribute' );

		if( !$db->hasColumn( 'mshop_attribute', 'key' )
			|| $db->table( 'mshop_attribute' )->col( 'key' )->length() === 255
		) {
			return;
		}

		$this->info( 'Update attribute "key" columns', 'vv' );

		$db->table( 'mshop_attribute' )->string( 'key', 255 )->default( '' )->up();

		$result = $db->stmt()->select( 'id', 'domain', 'type', 'code' )->from( 'mshop_attribute' )->execute();
		$db2 = $this->db( 'db-attribute', true );

		while( $row = $result->fetch() )
		{
			$key = substr( $row['domain'] . '|' . $row['type'] . '|' . $row['code'], 0, 255 );
			$db2->update( 'mshop_attribute', ['key' => $key], ['id' => $row['id']] );
		}

		$db2->close();
	}
}
