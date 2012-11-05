<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductTagTypeidAddConstraint.php 14336 2011-12-14 14:38:50Z nsendetzky $
 */


/**
 * Adds constraint for typeid in product tag table.
 */
class MW_Setup_Task_ProductTagTypeidAddConstraint extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
			'fk_msprota_typeid' => 'ALTER TABLE "mshop_product_tag" ADD CONSTRAINT "fk_msprota_typeid" FOREIGN KEY ("typeid") REFERENCES "mshop_product_tag_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
	 * Executes the SQL statements if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding constraint for table mshop_product_tag', 0 ); $this->_status( '' );

		if( $this->_schema->tableExists( 'mshop_product_tag' ) === true )
		{
			foreach ( $stmts as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->constraintExists( 'mshop_product_tag', $constraint ) === false )
				{
					$this->_execute( $stmt );
					$this->_status( 'added' );
				}
				else
				{
					$this->_status( 'OK' );
				}
			}
		}
	}
}
