<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Upscheme\Task;


class Tag extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Creating tag schema', 'vv' );
		$db = $this->db( 'db-tag' );

		foreach( $this->paths( 'default/schema/tag.php' ) as $filepath )
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
