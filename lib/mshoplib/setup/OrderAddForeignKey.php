<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds foreign key constraints for langid and curid columns in order base table.
 *
 * 2012-08-08
 * At this time the constrains are not needed anymore because of future dependency.
 * see: MW_Setup_Task_OrderDropForeignKeyOfLocale
 * -> Order domain table can be used on a differend database/ server
 */
class MW_Setup_Task_OrderAddForeignKey extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_base' => array(
			// 'fk_msordba_langid' => 'ALTER TABLE "mshop_order_base" ADD CONSTRAINT "fk_msordba_langid" FOREIGN KEY (`langid`) REFERENCES "mshop_global_language" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION',
			// 'fk_msordba_curid' => 'ALTER TABLE "mshop_order_base" ADD CONSTRAINT "fk_msordba_curid" FOREIGN KEY (`currencyid`) REFERENCES "mshop_global_currency" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION',
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
	 * Migrates service text data to list table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding foreign keys to mshop order base table', 0 ); $this->_status( '' );

		$refTableExists = $this->_schema->tableExists( 'mshop_global_language' )
			&& $this->_schema->tableExists( 'mshop_global_currency' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if ( $this->_schema->tableExists( $table ) === true && $refTableExists)
			{
				$this->_status( '' );

				foreach( $stmtList AS $constraint => $stmt )
				{
					$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

					if ( $this->_schema->constraintExists( $table, $constraint ) === false )
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
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
