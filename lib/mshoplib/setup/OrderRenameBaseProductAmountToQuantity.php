<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Renames column amount to quantity in order base product table.
 */
class MW_Setup_Task_OrderRenameBaseProductAmountToQuantity extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_order_base_product' => 
			'ALTER TABLE "mshop_order_base_product" CHANGE "amount" "quantity" INTEGER NOT NULL'
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
	 * Renames column amount to quantity if amount exists.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Renaming order base product amount to quantity', 0 ); $this->status( '' );
	
		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );
		
			if( $this->schema->tableExists( $table ) && $this->schema->columnExists( $table, 'amount' ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'renamed' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}	
}