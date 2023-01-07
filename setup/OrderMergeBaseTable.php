<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class OrderMergeBaseTable extends Base
{
	public function after() : array
	{
		return ['Order'];
	}


	public function before() : array
	{
		return ['MShopAddLocaleData'];
	}


	public function up()
	{
		$db = $this->db( 'db-order' );

		if( !$db->hasTable( 'mshop_order_base' ) ) {
			return;
		}

		$db->exec( '
			UPDATE ' . $db->qi( 'mshop_order' ) . ' AS dest
			SET ' . $db->qi( 'customerid' ) . ' = (
				SELECT ' . $db->qi( 'customerid' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'langid' ) . ' = (
				SELECT ' . $db->qi( 'langid' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'currencyid' ) . ' = (
				SELECT ' . $db->qi( 'currencyid' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'price' ) . ' = (
				SELECT ' . $db->qi( 'price' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'costs' ) . ' = (
				SELECT ' . $db->qi( 'costs' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'rebate' ) . ' = (
				SELECT ' . $db->qi( 'rebate' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'tax' ) . ' = (
				SELECT ' . $db->qi( 'tax' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'taxflag' ) . ' = (
				SELECT ' . $db->qi( 'taxflag' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'customerref' ) . ' = (
				SELECT ' . $db->qi( 'customerref' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			),' . $db->qi( 'comment' ) . ' = (
				SELECT ' . $db->qi( 'comment' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			)
			WHERE dest.' . $db->qi( 'baseid' ) . ' = (
				SELECT ' . $db->qi( 'id' ) . '
				FROM ' . $db->qi( 'mshop_order_base' ) . ' AS src
				WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
			)
		' );

		$db->dropForeign( 'mshop_order', 'fk_msord_baseid' )
			->dropColumn( 'mshop_order', 'baseid' )
			->dropTable( 'mshop_order_base' );
	}
}
