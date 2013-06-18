<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Renames the foreign key constraint on siteid for table mshop_attribute_list.
 */
class MW_Setup_Task_AttributeListRenameSiteidConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_list' => array(
			'fk_msattrli_siteid' =>  '
				ALTER TABLE "mshop_attribute_list"
				DROP FOREIGN KEY "fk_msattrli_siteid",
				ADD CONSTRAINT "fk_msattli_siteid" FOREIGN KEY ("siteid")
				REFERENCES "mshop_locale_site" ("id")
				ON DELETE CASCADE ON UPDATE CASCADE
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
	 * Renames attributelist siteid constraints if necessary.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming attributelist constraints', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			foreach ( $stmtList as $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->tableExists( $table ) && $this->_schema->constraintExists( $table, $constraint ) )
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
