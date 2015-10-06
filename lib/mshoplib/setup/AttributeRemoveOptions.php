<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Removes attribute option tables.
 */
class MW_Setup_Task_AttributeRemoveOptions extends MW_Setup_Task_Abstract
{
	private $mysql = array(
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
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'AttributeAddType' );
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
		$table = 'mshop_attribute';

		if( $this->schema->tableExists( $table ) === true )
		{
			$this->msg( sprintf( 'Adding columns to table "%1$s"', $table ), 0 ); $this->status( '' );

			foreach( $stmts['column'] as $column=>$stmt )
			{
				$this->msg( sprintf( 'Checking column "%1$s": ', $column ), 1 );

				if( $this->schema->columnExists( $table, $column ) === false )
				{
					$this->execute( $stmt );
					$this->status( 'added' );
				} else {
					$this->status( 'OK' );
				}
			}


			$this->msg( sprintf( 'Adding constraints to table "%1$s"', $table ), 0 ); $this->status( '' );

			foreach( $stmts['constraint'] as $constraint=>$stmt )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s": ', $constraint ), 1 );

				if( $this->schema->constraintExists( $table, $constraint ) === false )
				{
					$this->execute( $stmt );
					$this->status( 'added' );
				} else {
					$this->status( 'OK' );
				}
			}
		}

		unset( $table );


		// drop no longer required tables
		$this->msg( 'Delete attribute options', 0 ); $this->status( '' );

		foreach( $stmts['droptables'] as $table=>$stmt )
		{
			$this->msg( sprintf( 'Delete table "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->execute( $stmt );
				$this->status( 'deleted' );
			} else {
				$this->status( 'OK' );
			}
		}

	}
}