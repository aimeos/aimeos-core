<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes locale constraints from text tables.
 */
class MW_Setup_Task_TextDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_text_list_type' => array(
			'fk_mstexlity_siteid' => 'ALTER TABLE "mshop_text_list_type" DROP FOREIGN KEY "fk_mstexlity_siteid"',
		),
		'mshop_text_list' => array(
			'fk_mstexli_siteid' => 'ALTER TABLE "mshop_text_list" DROP FOREIGN KEY "fk_mstexli_siteid"',
		),
		'mshop_text_type' => array(
			'fk_mstexty_siteid' => 'ALTER TABLE "mshop_text_type" DROP FOREIGN KEY "fk_mstexty_siteid"',
		),
		'mshop_text' => array(
			'fk_mstex_siteid' => 'ALTER TABLE "mshop_text" DROP FOREIGN KEY "fk_mstex_siteid"',
			'fk_mstex_langid' => 'ALTER TABLE "mshop_text" DROP FOREIGN KEY "fk_mstex_langid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TextAddForeignKey' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Removing locale constraints from text tables', 0 );
		$this->_status( '' );

		$schema = $this->_getSchema( 'db-text' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->_execute( $stmt, 'db-text' );
						$this->_status( 'done' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}