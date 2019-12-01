<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
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
		return ['TypesMigrateColumns'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->msg( 'Update lists "key" columns', 0 ); $this->status( '' );

		foreach( $this->tables as $rname => $table )
		{
			$schema = $this->getSchema( $rname );

			$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

			if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'key' ) )
			{
				$dbm = $this->additional->getDatabaseManager();
				$conn = $dbm->acquire( $rname );

				$select = sprintf( 'SELECT "id", "domain", "type", "refid" FROM "%1$s" WHERE "key"=\'\'', $table );
				$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

				$stmt = $conn->create( $update );
				$result = $conn->create( $select )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['domain'] . '|' . $row['type'] . '|' . $row['refid'] );
					$stmt->bind( 2, $row['id'] );
					$stmt->execute()->finish();
				}

				$dbm->release( $conn, $rname );

				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
