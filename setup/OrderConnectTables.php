<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderConnectTables extends Base
{
	public function before() : array
	{
		return ['Order'];
	}


	public function up()
	{
		$this->address()->coupon()->product()->service()->subscription();
	}


	protected function address()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_address', 'baseid' ) ) {
			return $this;
		}

		$db->dropForeign( 'mshop_order_base_address', 'fk_msordbaad_baseid' );
		$db->dropIndex( 'mshop_order_base_address', 'unq_msordbaad_bid_type' );

		$db->table( 'mshop_order_base_address' )->bigint( 'parentid' )->null( true )->up();

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_order_base_address' ) . ' AS dest
			SET ' . $db->qi( 'parentid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'baseid' ) . '
			)
		' );

		$db->dropColumn( 'mshop_order_base_address', 'baseid' );

		return $this;
	}


	protected function coupon()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_coupon', 'baseid' ) ) {
			return $this;
		}

		$db->dropForeign( 'mshop_order_base_coupon', 'fk_msordbaco_baseid' );

		$db->table( 'mshop_order_base_coupon' )->bigint( 'parentid' )->null( true )->up();

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_order_base_coupon' ) . ' AS dest
			SET ' . $db->qi( 'parentid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'baseid' ) . '
			)
		' );

		$db->dropColumn( 'mshop_order_base_coupon', 'baseid' );

		return $this;
	}


	protected function product()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_product', 'baseid' ) ) {
			return $this;
		}

		$db->dropForeign( 'mshop_order_base_product', 'fk_msordbapr_baseid' );
		$db->dropIndex( 'mshop_order_base_product', 'unq_msordbapr_bid_pos' );

		$db->table( 'mshop_order_base_product' )->bigint( 'parentid' )->null( true )->up();

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_order_base_product' ) . ' AS dest
			SET ' . $db->qi( 'parentid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'baseid' ) . '
			)
		' );

		$db->dropColumn( 'mshop_order_base_product', 'baseid' );

		return $this;
	}


	protected function service()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_order_base_service', 'baseid' ) ) {
			return $this;
		}

		$db->dropForeign( 'mshop_order_base_service', 'fk_msordbase_baseid' );
		$db->dropIndex( 'mshop_order_base_service', ['unq_msordbase_bid_cd_typ_sid', 'unq_msordbase_bid_sid_cd_typ'] );

		$db->table( 'mshop_order_base_service' )->bigint( 'parentid' )->null( true )->up();

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_order_base_service' ) . ' AS dest
			SET ' . $db->qi( 'parentid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'baseid' ) . '
			)
		' );

		$db->dropColumn( 'mshop_order_base_service', 'baseid' );

		return $this;
	}


	protected function subscription()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasColumn( 'mshop_subscription', 'baseid' ) ) {
			return $this;
		}

		$db->dropIndex( 'mshop_subscription', 'idx_mssub_baseid' );

		$db->table( 'mshop_subscription' )->bigint( 'orderid' )->null( true )->up();

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_subscription' ) . ' AS dest
			SET ' . $db->qi( 'orderid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'baseid' ) . '
			)
		' );

		$db->dropColumn( 'mshop_subscription', 'baseid' );

		return $this;
	}
}
