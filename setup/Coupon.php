<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Upscheme\Task;


class Coupon extends Base
{
	public function before() : array
	{
		return ['Locale'];
	}


	public function up()
	{
		$this->info( 'Creating coupon schema', 'vv' );
		$db = $this->db( 'db-coupon' );

		foreach( $this->paths( 'default/schema/coupon.php' ) as $filepath )
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
