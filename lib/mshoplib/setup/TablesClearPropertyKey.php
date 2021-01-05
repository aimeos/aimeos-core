<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Clear key columns
 */
class TablesClearPropertyKey extends \Aimeos\MW\Setup\Task\Base
{
	private $tables = [
		'db-attribute' => 'mshop_attribute_property',
		'db-customer' => 'mshop_customer_property',
		'db-media' => 'mshop_media_property',
		'db-price' => 'mshop_price_property',
		'db-product' => 'mshop_product_property',
	];


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
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
		$this->msg( 'Clear property "key" columns', 0, '' );

		foreach( $this->tables as $rname => $table )
		{
			$schema = $this->getSchema( $rname );

			$this->msg( sprintf( 'Checking table %1$s', $table ), 1 );

			if( $schema->tableExists( $table ) && $schema->columnExists( $table, 'key' )
				&& ( $column = $schema->getColumnDetails( $table, 'key' ) ) && $column->getMaxLength() !== 103
			) {
				$dbm = $this->additional->getDatabaseManager();
				$conn = $dbm->acquire( $rname );

				$conn->create( sprintf( 'UPDATE "%1$s" SET "key" = \'\'', $table ) )->execute()->finish();

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
