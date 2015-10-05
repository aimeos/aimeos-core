<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds unique constraint on siteid and code for table mshop_customer.
 */
class CustomerChangeCodeSiteidUnique extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_customer' => array(
			'unq_mscus_sid_code' => '
				ALTER TABLE "mshop_customer" ADD CONSTRAINT "unq_mscus_sid_code" UNIQUE ("siteid", "code")
			',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CustomerAddColumns', 'TablesChangeSiteidNotNull' );
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
	 * Changes UNIQUE constraint for customer if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing customer unique constraint', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach( $stmtList as $constraint=>$stmt )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->schema->tableExists( $table ) && !$this->schema->constraintExists( $table, $constraint ) )
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
}
