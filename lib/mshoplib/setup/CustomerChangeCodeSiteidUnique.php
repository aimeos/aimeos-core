<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds unique constraint on siteid and code for table mshop_customer.
 */
class MW_Setup_Task_CustomerChangeCodeSiteidUnique extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_customer' => array(
			'unq_mscus_sid_code' => '
				ALTER TABLE "mshop_customer" ADD CONSTRAINT "unq_mscus_sid_code" UNIQUE ("siteid", "code")
			',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('CustomerAddColumns', 'TablesChangeSiteidNotNull');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Changes UNIQUE constraint for customer if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Changing customer unique constraint', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->tableExists( $table ) && !$this->_schema->constraintExists( $table, $constraint ) )
				{
					$this->_execute( $stmt );
					$this->_status( 'changed' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}
