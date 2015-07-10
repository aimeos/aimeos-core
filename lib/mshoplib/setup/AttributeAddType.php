<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds typeid column to attribute table.
 */
class MW_Setup_Task_AttributeAddType extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute' => array (
			'typeid' => array(
				'ALTER TABLE "mshop_attribute" ADD "typeid" INTEGER DEFAULT NULL AFTER "siteid"',
				'ALTER TABLE `mshop_attribute` ADD CONSTRAINT `fk_msattr_typeid` FOREIGN KEY (`typeid`) REFERENCES `mshop_attribute_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
			),
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
	 * Add column to table if the column doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{

		foreach( $stmts AS $table=>$columns )
		{
			$this->_msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->_status( '' );

			if( $this->_schema->tableExists( $table ) === true )
			{
				foreach ( $columns AS $column=>$stmtList )
				{
					$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

					if( $this->_schema->columnExists( $table, $column ) === false )
					{
						$this->_executeList( $stmtList );
						$this->_status( 'added' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}