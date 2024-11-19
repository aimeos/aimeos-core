<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2024
 */


namespace Aimeos\Upscheme\Task;


class OrderRenameTables extends Base
{
	public function after() : array
	{
		return [
			'OrderAddProductParentid', 'OrderConnectTables',
			'OrderRenameAttributeParentid', 'OrderRenameProductStatus', 'OrderRenameProductSupplier',
		];
	}


	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->constraints()->indexes()->tables();
	}


	protected function constraints()
	{
		$this->db( 'db-order' )
			->dropForeign( 'mshop_order_base_product_attr', 'fk_msordbaprat_parentid' )
			->dropForeign( 'mshop_order_base_service_attr', 'fk_msordbaseat_parentid' )
			->dropForeign( 'mshop_order_base_service_tx', 'fk_msordbasetx_parentid' );

		return $this;
	}


	protected function indexes()
	{
		$this->db( 'db-order' )
			->dropIndex( 'mshop_order_base_product_attr', [
				'unq_msordbaprat_oid_aid_ty_cd',
				'idx_msordbaprat_si_cd_va'
			] )
			->dropIndex( 'mshop_order_base_service_attr', [
				'unq_msordbaseat_oid_aid_ty_cd',
				'idx_msordbaseat_si_cd_va'
			] );

		return $this;
	}


	protected function tables()
	{
		$this->db( 'db-order' )->renameTable( [
			'mshop_order_base_address' => 'mshop_order_address',
			'mshop_order_base_coupon' => 'mshop_order_coupon',
			'mshop_order_base_product' => 'mshop_order_product',
			'mshop_order_base_product_attr' => 'mshop_order_product_attr',
			'mshop_order_base_service' => 'mshop_order_service',
			'mshop_order_base_service_attr' => 'mshop_order_service_attr',
			'mshop_order_base_service_tx' => 'mshop_order_service_tx'
		] );

		return $this;
	}
}
