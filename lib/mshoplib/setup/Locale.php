<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class Locale extends Base
{
	public function up()
	{
		$this->info( 'Creating locale schema', 'v' );
		$db = $this->db( 'db-locale' );

		foreach( $this->paths( 'default/schema/locale.php' ) as $filepath )
		{
			if( ( $list = include( $filepath ) ) === false ) {
				throw new \RuntimeException( sprintf( 'Unable to get schema from file "%1$s"', $filepath ) );
			}

			foreach( $list['table'] ?? [] as $name => $fcn ) {
				$db->table( $name, $fcn );
				$db->up();
			}
		}
	}
}
