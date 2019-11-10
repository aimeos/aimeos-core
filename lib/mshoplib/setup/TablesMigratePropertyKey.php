<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
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
	public function getPreDependencies()
	{
		return ['TypesMigrateColumns'];
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
		$this->msg( 'Update property "key" columns', 0 ); $this->status( '' );

		foreach( $this->tables as $rname => $table )
		{
			$schema = $this->getSchema( $rname );

			$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

			if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'key' )
				&& ( $item = $schema->getColumnDetails( $table, 'key' ) ) && ( $item->getMaxLength() !== 130 )
			) {
				$dbm = $this->additional->getDatabaseManager();
				$conn = $dbm->acquire( $rname );

				$select = sprintf( 'SELECT "id", "type", "langid", "value" FROM "%1$s"', $table );
				$update = sprintf( 'UPDATE "%1$s" SET "key" = ? WHERE "id" = ?', $table );

				$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );
				$result = $conn->create( $select )->execute();

				while( ( $row = $result->fetch() ) !== false )
				{
					$stmt->bind( 1, $row['type'] . '|' . ( $row['langid'] ?: 'null' ) . '|' . md5( $row['value'] ) );
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
