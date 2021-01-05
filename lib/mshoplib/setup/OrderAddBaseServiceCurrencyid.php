<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds currency ID values to order base product tables.
 */
class OrderAddBaseServiceCurrencyid extends \Aimeos\MW\Setup\Task\Base
{
	private $sql = '
		UPDATE "mshop_order_base_service" SET "currencyid" = (
			SELECT ob."currencyid" FROM "mshop_order_base" ob WHERE ob."id" = "baseid"
		) WHERE "currencyid" = \'\'  OR "currencyid" = \'   \'
	';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the migration task
	 */
	public function migrate()
	{
		$this->msg( 'Adding currency ID to order base service table', 0 );
		$schema = $this->getSchema( 'db-order' );

		if( $schema->tableExists( 'mshop_order_base' ) && $schema->tableExists( 'mshop_order_base_product' ) ) {
			$this->execute( $this->sql, 'db-order' );
		}

		$this->status( 'done' );
	}
}
