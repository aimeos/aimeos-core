<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds label and status to warehouse table.
 */
class ProductWarehouseAddLabelStatus extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_product_warehouse' => array(
			'label'  => 'ALTER TABLE "mshop_product_warehouse" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_product_warehouse" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER label',
		),
	);

	private $update = array(
		'mshop_product_warehouse' => array(
			'status' => 'UPDATE "mshop_product_warehouse" SET status = 1 WHERE label = \'\'',
			'label' => 'UPDATE "mshop_product_warehouse" SET label = code WHERE label = \'\'',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return [];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->msg( sprintf( 'Adding label and status columns for product warehouse' ), 0 );
		$this->status( '' );

		foreach( $this->mysql as $table => $columns ) {

			if( $this->schema->tableExists( $table ) ) {

				foreach( $columns as $column => $stmt ) {

					$this->msg( sprintf( 'Checking column "%1$s.%2$s": ', $table, $column ), 1 );

					if( !$this->schema->columnExists( $table, $column ) ) {
						$this->execute( $stmt );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}


		foreach( $this->update as $table => $columns ) {

			if( $this->schema->tableExists( $table ) ) {

				foreach( $columns as $column => $stmt ) {

					$this->msg( sprintf( 'Update column "%1$s.%2$s": ', $table, $column ), 1 );

					if( $this->schema->columnExists( $table, $column ) ) {
						$this->execute( $stmt );
						$this->status( 'updated' );
					}
				}
			}
		}

	}
}