<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameAttributeParentid extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );


		if( $db->hasTable( 'mshop_order_base_product_attr' )
			&& !$db->hasColumn( 'mshop_order_base_product_attr', 'parentid' )
		) {
			$this->info( 'Rename "ordprodid" to "parentid" in "mshop_order_base_product_attr" table', 'v' );

			$db->dropForeign( 'mshop_order_base_product_attr', 'fk_msordbaprat_ordprodid' )
				->dropIndex( 'mshop_order_base_product_attr', 'fk_msordbaprat_ordprodid' )
				->dropIndex( 'mshop_order_base_product_attr', 'unq_msordbaprat_oid_aid_ty_cd' )
				->renameColumn( 'mshop_order_base_product_attr', 'ordprodid', 'parentid' );
		}


		if( $db->hasTable( 'mshop_order_base_service_attr' )
			&& !$db->hasColumn( 'mshop_order_base_service_attr', 'parentid' )
		) {
			$this->info( 'Rename "ordservid" to "parentid" in "mshop_order_base_service_attr" table', 'v' );

			$db->dropForeign( 'mshop_order_base_service_attr', 'fk_msordbaseat_ordservid' )
				->dropIndex( 'mshop_order_base_service_attr', 'fk_msordbaseat_ordservid' )
				->dropIndex( 'mshop_order_base_service_attr', 'unq_msordbaseat_oid_aid_ty_cd' )
				->renameColumn( 'mshop_order_base_service_attr', 'ordservid', 'parentid' );
		}
	}
}
