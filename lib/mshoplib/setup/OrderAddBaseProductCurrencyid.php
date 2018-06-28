<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds currency ID values to order base product tables.
 */
class OrderAddBaseProductCurrencyid extends \Aimeos\MW\Setup\Task\Base
{
	private $sql = '
		UPDATE "mshop_order_base_product" SET "currencyid" = (
			SELECT ob."currencyid" FROM "mshop_order_base" ob WHERE ob."id" = "baseid" LIMIT 1
		) WHERE "currencyid" = \'\'  OR "currencyid" = \'   \'
	';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the migration task
	 */
	public function migrate()
	{
		$this->msg( 'Adding currency ID to order base product table', 0 );

		$this->execute( $this->sql, 'db-order' );

		$this->status( 'done' );
	}
}