<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameTables extends Base
{
	public function before() : array
	{
		return [
			'OrderAddProductParentid', 'OrderAddBaseServiceCurrencyid', 'OrderAddBaseProductCurrencyid',
			'OrderRenameAttributeParentid', 'OrderRenameProductStatus', 'OrderRenameProductSupplier'
		];
	}


	public function up()
	{
		$this->info( 'Rename mshop_order_order_base_* tables', 'vv' );

		$this->constraints()->indexes()->tables();
	}


	protected function constraints()
	{
		$db = $this->db( 'db-order' );

		if( $db->hasForeign( 'mshop_order_base_product_attr', 'fk_msordbaprat_parentid' ) ) {
			$db->dropForeign( 'mshop_order_base_product_attr', 'fk_msordbaprat_parentid' );
		}

		if( $db->hasForeign( 'mshop_order_base_service_attr', 'fk_msordbaseat_parentid' ) ) {
			$db->dropForeign( 'mshop_order_base_service_attr', 'fk_msordbaseat_parentid' );
		}

		if( $db->hasForeign( 'mshop_order_base_service_tx', 'fk_msordbasetx_parentid' ) ) {
			$db->dropForeign( 'mshop_order_base_service_tx', 'fk_msordbasetx_parentid' );
		}

		return $this;
	}


	protected function indexes()
	{
		$db = $this->db( 'db-order' );

		if( $db->hasIndex( 'mshop_order_base_product_attr', 'unq_msordbaprat_oid_aid_ty_cd' ) ) {
			$db->dropIndex( 'mshop_order_base_product_attr', 'unq_msordbaprat_oid_aid_ty_cd' );
		}

		if( $db->hasIndex( 'mshop_order_base_product_attr', 'idx_msordbaprat_si_cd_va' ) ) {
			$db->dropIndex( 'mshop_order_base_product_attr', 'idx_msordbaprat_si_cd_va' );
		}

		if( $db->hasIndex( 'mshop_order_base_service_attr', 'unq_msordbaseat_oid_aid_ty_cd' ) ) {
			$db->dropIndex( 'mshop_order_base_service_attr', 'unq_msordbaseat_oid_aid_ty_cd' );
		}

		if( $db->hasIndex( 'mshop_order_base_service_attr', 'idx_msordbaseat_si_cd_va' ) ) {
			$db->dropIndex( 'mshop_order_base_service_attr', 'idx_msordbaseat_si_cd_va' );
		}

		return $this;
	}


	protected function tables()
	{
		$db = $this->db( 'db-order' );

		if( $db->hasTable( 'mshop_order_base_address' ) ) {
			$db->renameTable( 'mshop_order_base_address', 'mshop_order_address' );
		}

		if( $db->hasTable( 'mshop_order_base_coupon' ) ) {
			$db->renameTable( 'mshop_order_base_coupon', 'mshop_order_coupon' );
		}

		if( $db->hasTable( 'mshop_order_base_product' ) ) {
			$db->renameTable( 'mshop_order_base_product', 'mshop_order_product' );
		}

		if( $db->hasTable( 'mshop_order_base_product_attr' ) ) {
			$db->renameTable( 'mshop_order_base_product_attr', 'mshop_order_product_attr' );
		}

		if( $db->hasTable( 'mshop_order_base_service' ) ) {
			$db->renameTable( 'mshop_order_base_service', 'mshop_order_service' );
		}

		if( $db->hasTable( 'mshop_order_base_service_attr' ) ) {
			$db->renameTable( 'mshop_order_base_service_attr', 'mshop_order_service_attr' );
		}

		if( $db->hasTable( 'mshop_order_base_service_tx' ) ) {
			$db->renameTable( 'mshop_order_base_service_tx', 'mshop_order_service_tx' );
		}

		return $this;
	}
}
