<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds typeid column to attribute table.
 */
class AttributeAddType extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_attribute' => array(
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

		foreach( $stmts as $table=>$columns )
		{
			$this->msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->status( '' );

			if( $this->schema->tableExists( $table ) === true )
			{
				foreach( $columns as $column=>$stmtList )
				{
					$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

					if( $this->schema->columnExists( $table, $column ) === false )
					{
						$this->executeList( $stmtList );
						$this->status( 'added' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}