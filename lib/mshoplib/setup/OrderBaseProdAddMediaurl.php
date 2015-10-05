<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds mediaurl column to table mshop_order_base_product.
 */
class OrderBaseProdAddMediaurl extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mediaurl' => 'ALTER TABLE "mshop_order_base_product" ADD "mediaurl" VARCHAR(255) NOT NULL AFTER "name"',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderBaseProductRenameLabelToName', 'OrderRenameTables' );
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
		$this->process( $this->mysql );
	}


	/**
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$table = "mshop_order_base_product";
		$this->msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 );
		$this->status( '' );

		foreach( $stmts as $column => $stmt ) {
			$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

			if( $this->schema->tableExists( $table ) === true &&
				$this->schema->columnExists( $table, $column ) === false ) {
				$this->execute( $stmt );
				$this->status( 'added' );
			} else {
				$this->status( 'OK' );
			}
		}
	}

}