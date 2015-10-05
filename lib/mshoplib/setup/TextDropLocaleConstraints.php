<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes locale constraints from text tables.
 */
class TextDropLocaleConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
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
		$this->msg( 'Removing locale constraints from text tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-text' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-text' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}