<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes locale constraints from media tables.
 */
class MW_Setup_Task_MediaDropLocaleConstraints extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_media_list_type' => array(
			'fk_msmedlity_siteid' => 'ALTER TABLE "mshop_media_list_type" DROP FOREIGN KEY "fk_msmedlity_siteid"',
		),
		'mshop_media_list' => array(
			'fk_msmedli_siteid' => 'ALTER TABLE "mshop_media_list" DROP FOREIGN KEY "fk_msmedli_siteid"',
		),
		'mshop_media_type' => array(
			'fk_msmedty_siteid' => 'ALTER TABLE "mshop_media_type" DROP FOREIGN KEY "fk_msmedty_siteid"',
		),
		'mshop_media' => array(
			'fk_msmed_siteid' => 'ALTER TABLE "mshop_media" DROP FOREIGN KEY "fk_msmed_siteid"',
			'fk_msmed_typeid' => 'ALTER TABLE "mshop_media" DROP FOREIGN KEY "fk_msmed_typeid"',
			'fk_msmed_langid' => 'ALTER TABLE "mshop_media" DROP FOREIGN KEY "fk_msmed_langid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MediaAddForeignKey' );
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
		$this->_msg( 'Removing locale constraints from media tables', 0 );
		$this->_status( '' );

		$schema = $this->_getSchema( 'db-media' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->_execute( $stmt, 'db-media' );
						$this->_status( 'done' );
					} else {
						$this->_status( 'OK' );
					}
				}
			}
		}
	}
}