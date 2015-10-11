<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes collation of mshop_order_base_product.
 */
class OrderChangeBaseProductCodeCollationUtf8bin extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'prodcode' => 'ALTER TABLE "mshop_order_base_product" MODIFY "prodcode" VARCHAR(32) NOT NULL COLLATE utf8_bin',
		'suppliercode' => 'ALTER TABLE "mshop_order_base_product" MODIFY "suppliercode" VARCHAR(32) NOT NULL COLLATE utf8_bin'
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Changes collation of mshop_order_base_product.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$tablename = 'mshop_order_base_product';
		
		$this->msg( 'Changing collation in mshop_order_base_product', 0 );
		$this->status( '' );
		
		foreach( $stmts as $columnname => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s": ', $columnname ), 1 );
			
			if( $this->schema->tableExists( $tablename ) === true
				&& $this->schema->columnExists( $tablename, $columnname ) === true
				&& $this->schema->getColumnDetails( $tablename, $columnname )->getCollationType() !== 'utf8_bin' )
			{
				$this->execute( $stmt );
				$this->status( 'changed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
	
}