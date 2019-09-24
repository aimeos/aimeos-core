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
		'db-attribute' => 'mshop_attribute_lists',
		'db-catalog' => 'mshop_catalog_lists',
		'db-customer' => 'mshop_customer_lists',
		'db-media' => 'mshop_media_lists',
		'db-price' => 'mshop_price_lists',
		'db-product' => 'mshop_product_lists',
		'db-service' => 'mshop_service_lists',
		'db-supplier' => 'mshop_supplier_lists',
		'db-text' => 'mshop_text_lists',
	];


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
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

				$select = sprintf( 'SELECT "id", "domain", "type", "refid" FROM "%1$s"', $table );
				$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

				$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
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
