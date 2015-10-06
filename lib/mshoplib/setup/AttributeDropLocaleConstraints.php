<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes locale constraints from attribute tables.
 */
class MW_Setup_Task_AttributeDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $mysql = array(
		'mshop_attribute_list_type' => array(
			'fk_msattlity_siteid' => 'ALTER TABLE "mshop_attribute_list_type" DROP FOREIGN KEY "fk_msattlity_siteid"',
		),
		'mshop_attribute_list' => array(
			'fk_msattli_siteid' => 'ALTER TABLE "mshop_attribute_list" DROP FOREIGN KEY "fk_msattli_siteid"',
		),
		'mshop_attribute_type' => array(
			'fk_msattty_siteid' => 'ALTER TABLE "mshop_attribute_type" DROP FOREIGN KEY "fk_msattty_siteid"',
		),
		'mshop_attribute' => array(
			'fk_msattr_siteid' => 'ALTER TABLE "mshop_attribute" DROP FOREIGN KEY "fk_msattr_siteid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'AttributeListRenameSiteidConstraints' );
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
	 * Drops local constraints.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Removing locale constraints from attribute tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-attribute' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-attribute' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}