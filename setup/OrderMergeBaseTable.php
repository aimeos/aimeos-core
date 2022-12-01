<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
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
			UPDATE ' . $db->qi( 'mshop_order' ) . ' AS dest, ( SELECT * FROM ' . $db->qi( 'mshop_order_base' ) . ' ) AS src
			SET dest.' . $db->qi( 'customerid' ) . ' = src.' . $db->qi( 'customerid' ) . ',
				dest.' . $db->qi( 'langid' ) . ' = src.' . $db->qi( 'langid' ) . ',
				dest.' . $db->qi( 'currencyid' ) . ' = src.' . $db->qi( 'currencyid' ) . ',
				dest.' . $db->qi( 'price' ) . ' = src.' . $db->qi( 'price' ) . ',
				dest.' . $db->qi( 'costs' ) . ' = src.' . $db->qi( 'costs' ) . ',
				dest.' . $db->qi( 'rebate' ) . ' = src.' . $db->qi( 'rebate' ) . ',
				dest.' . $db->qi( 'tax' ) . ' = src.' . $db->qi( 'tax' ) . ',
				dest.' . $db->qi( 'taxflag' ) . ' = src.' . $db->qi( 'taxflag' ) . ',
				dest.' . $db->qi( 'customerref' ) . ' = src.' . $db->qi( 'customerref' ) . ',
				dest.' . $db->qi( 'comment' ) . ' = src.' . $db->qi( 'comment' ) . '
			WHERE dest.' . $db->qi( 'baseid' ) . ' = src.' . $db->qi( 'id' ) . '
		' );

		$db->dropForeign( 'mshop_order', 'fk_msord_baseid' )
			->dropColumn( 'mshop_order', 'baseid' )
			->dropTable( 'mshop_order_base' );
	}
}
