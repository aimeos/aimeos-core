<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\Upscheme\Task;


class Group extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Creating group schema', 'vv' );
		$db = $this->db( 'db-group' );

		foreach( $this->paths( 'default/schema/group.php' ) as $filepath )
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
