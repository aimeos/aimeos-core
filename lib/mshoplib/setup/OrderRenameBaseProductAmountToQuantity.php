<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Renames column amount to quantity in order base product table.
 */
class OrderRenameBaseProductAmountToQuantity extends \Aimeos\MW\Setup\Task\Base
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