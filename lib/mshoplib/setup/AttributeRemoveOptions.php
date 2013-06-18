<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Removes attribute option tables.
 */
class MW_Setup_Task_AttributeRemoveOptions extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'column' => array(
			'label' => 'ALTER TABLE "mshop_attribute" ADD "label" VARCHAR(255) NOT NULL AFTER "code"',
			'pos' => 'ALTER TABLE "mshop_attribute" ADD "pos" INT DEFAULT NULL AFTER "label"'
		),
		'constraint' => array(
			'unq_msattr_sid_dom_cod_tid' => 'ALTER TABLE "mshop_attribute" DROP INDEX "unq_msattr_sid_dom_cod", ADD CONSTRAINT "unq_msattr_sid_dom_cod_tid" UNIQUE ("siteid", "domain", "code", "typeid")'
		),
		'droptables' => array(
			'mshop_attribute_option_list' => 'DROP TABLE `mshop_attribute_option_list`',
			'mshop_attribute_option_list_type' => 'DROP TABLE `mshop_attribute_option_list_type`',
			'mshop_attribute_option' => 'DROP TABLE `mshop_attribute_option`',
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array('AttributeAddType');
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
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
		$table = 'mshop_attribute';

		if( $this->_schema->tableExists( $table ) === true )
		{
			$this->_msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->_status( '' );

			foreach ( $stmts['column'] AS $column=>$stmt )
			{
				$this->_msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

				if( $this->_schema->columnExists( $table, $column ) === false )
				{
					$this->_execute( $stmt );
					$this->_status( 'added' );
				} else {
					$this->_status( 'OK' );
				}
			}


			$this->_msg( sprintf( 'Adding constraints to table "%1$s"', $table ), 0 ); $this->_status( '' );

			foreach ( $stmts['constraint'] AS $constraint=>$stmt )
			{
				$this->_msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->_schema->constraintExists( $table, $constraint ) === false )
				{
					$this->_execute( $stmt );
					$this->_status( 'added' );
				} else {
					$this->_status( 'OK' );
				}
			}
		}

		unset($table);


		// drop no longer required tables
		$this->_msg( 'Delete attribute options', 0 ); $this->_status( '' );

		foreach( $stmts['droptables'] AS $table=>$stmt )
		{
			$this->_msg( sprintf( 'Delete table "%1$s"', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'deleted' );
			} else {
				$this->_status( 'OK' );
			}
		}

	}
}