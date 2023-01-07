<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class Catalog extends Base
{
	public function up()
	{
		$this->info( 'Creating catalog schema', 'vv' );
		$db = $this->db( 'db-catalog' );

		foreach( $this->paths( 'default/schema/catalog.php' ) as $filepath )
		{
			if( ( $list = include( $filepath ) ) === false ) {
				throw new \RuntimeException( sprintf( 'Unable to get schema from file "%1$s"', $filepath ) );
			}

			foreach( $list['table'] ?? [] as $name => $fcn ) {
				$db->table( $name, $fcn );
			}
		}
	}
}
