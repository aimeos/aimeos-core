<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Updates key columns
 */
class TablesMigratePropertyKey extends \Aimeos\MW\Setup\Task\Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_property',
		'db-customer' => 'mshop_customer_property',
		'db-media' => 'mshop_media_property',
		'db-price' => 'mshop_price_property',
		'db-product' => 'mshop_product_property',
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TypesMigrateColumns', 'TablesClearPropertyKey', 'TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update property "key" columns', 0 ); $this->status( '' );

		foreach( $this->tables as $rname => $table )
		{
			$schema = $this->getSchema( $rname );

			$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

			$dbm = $this->additional->getDatabaseManager();
			$cselect = $dbm->acquire( $rname );
			$cupdate = $dbm->acquire( $rname );

			$select = sprintf( 'SELECT "id", "type", "langid", "value" FROM "%1$s" WHERE "key"=\'\'', $table );
			$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

			$stmt = $cupdate->create( $update );
			$result = $cselect->create( $select )->execute();

			while( ( $row = $result->fetch() ) !== null )
			{
				$stmt->bind( 1, $row['type'] . '|' . ( $row['langid'] ?: 'null' ) . '|' . md5( $row['value'] ) );
				$stmt->bind( 2, $row['id'] );
				$stmt->execute()->finish();
			}

			$dbm->release( $cupdate, $rname );
			$dbm->release( $cselect, $rname );

			$this->status( 'done' );
		}
	}
}
