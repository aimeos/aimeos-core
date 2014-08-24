<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes default product type and label values.
 */
class MW_Setup_Task_ProductChangeTypeCodeProductToDefault extends MW_Setup_Task_Abstract
{
	private $_mysql = 'UPDATE "mshop_product_type" SET "code" = \'default\', "label" = \'Article\' WHERE "code" = \'product\'';


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TypesAddLabelStatus' );
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
	 * Executes the task.
	 *
	 * @param string $stmt SQL statement to execute
	 */
	protected function _process( $stmt )
	{
		$msg = 'Changing code from "product" to "default" in "mshop_product_type"';
		$this->_msg($msg, 0);

		if ($this->_schema->tableExists('mshop_product_type') )
		{
			$result = $this->_conn->create( $stmt )->execute();
			$cntRows = $result->affectedRows();
			$result->finish();

			if ( $cntRows ) {
				$this->_status( sprintf( 'migrated (%1$d)', $cntRows ) );
			} else {
				$this->_status( 'OK' );
			}
		}
		else
		{
			$this->_status('OK');
		}
	}

}