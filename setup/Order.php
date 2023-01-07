<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Upscheme\Task;


class Order extends Base
{
	public function up()
	{
		$this->info( 'Creating order schema', 'vv' );
		$db = $this->db( 'db-order' );

		foreach( $this->paths( 'default/schema/order.php' ) as $filepath )
		{
			if( ( $list = include( $filepath ) ) === false ) {
				throw new \RuntimeException( sprintf( 'Unable to get schema from file "%1$s"', $filepath ) );
			}

			foreach( $list['table'] ?? [] as $name => $fcn ) {
				$db->table( $name, $fcn );
			}
		}

		$db->up();

		if( !$db->hasIndex( 'mshop_order_product_attr', 'idx_msordprat_si_cd_va' ) )
		{
			$db->for( 'mysql', 'CREATE INDEX `idx_msordprat_si_cd_va` ON `mshop_order_product_attr` (`siteid`, `code`, `value`(16))' );
			$db->for( 'postgresql', 'CREATE INDEX "idx_msordprat_si_cd_va" ON "mshop_order_product_attr" ("siteid", "code", left("value", 16))' );
			$db->for( 'mssql', 'CREATE INDEX "idx_msordprat_si_cd_va" ON "mshop_order_product_attr" ("siteid", "code")' );
		}

		if( !$db->hasIndex( 'mshop_order_service_attr', 'idx_msordseat_si_cd_va' ) )
		{
			$db->for( 'mysql', 'CREATE INDEX `idx_msordseat_si_cd_va` ON `mshop_order_service_attr` (`siteid`, `code`, `value`(16))' );
			$db->for( 'postgresql', 'CREATE INDEX "idx_msordseat_si_cd_va" ON "mshop_order_service_attr" ("siteid", "code", left("value", 16))' );
			$db->for( 'mssql', 'CREATE INDEX "idx_msordseat_si_cd_va" ON "mshop_order_service_attr" ("siteid", "code")' );
		}
	}
}
