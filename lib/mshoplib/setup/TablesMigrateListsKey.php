<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates key columns
 */
class TablesMigrateListsKey extends \Aimeos\MW\Setup\Task\Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_list',
		'db-catalog' => 'mshop_catalog_list',
		'db-customer' => 'mshop_customer_list',
		'db-media' => 'mshop_media_list',
		'db-price' => 'mshop_price_list',
		'db-product' => 'mshop_product_list',
		'db-service' => 'mshop_service_list',
		'db-supplier' => 'mshop_supplier_list',
		'db-text' => 'mshop_text_list',
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TypesMigrateColumns', 'TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update lists "key" columns', 0 ); $this->status( '' );

		$this->process( $this->tables );
	}


	protected function process( array $tables )
	{
		foreach( $tables as $rname => $table )
		{
			$count = 0;
			$schema = $this->getSchema( $rname );

			$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

			$dbm = $this->additional->getDatabaseManager();
			$cselect = $dbm->acquire( $rname );
			$cupdate = $dbm->acquire( $rname );

			$select = sprintf( 'SELECT "id", "domain", "type", "refid" FROM "%1$s" WHERE "key"=\'\'', $table );
			$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

			$stmt = $cupdate->create( $update );
			$result = $cselect->create( $select )->execute();

			while( ( $row = $result->fetch() ) !== null )
			{
				$stmt->bind( 1, $row['domain'] . '|' . $row['type'] . '|' . $row['refid'] );
				$stmt->bind( 2, $row['id'] );
				$stmt->execute()->finish();
				$count++;
			}

			$dbm->release( $cupdate, $rname );
			$dbm->release( $cselect, $rname );

			$this->status( $count > 0 ? 'done' : 'OK' );
		}
	}
}
