<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Moves product tag to own domain
 */
class TagMoveProductTag extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_product_tag_type' => array(
			'RENAME TABLE "mshop_product_tag_type" TO "mshop_tag_type"',
			'ALTER TABLE "mshop_tag_type" DROP INDEX "unq_msprotaty_sid_dom_code", ADD UNIQUE INDEX "unq_mstagty_sid_dom_code" ("siteid", "domain", "code")',
			'ALTER TABLE "mshop_tag_type" DROP INDEX "idx_msprotaty_sid_status", ADD INDEX "idx_mstagty_sid_status" ("siteid", "status")',
			'ALTER TABLE "mshop_tag_type" DROP INDEX "idx_msprotaty_sid_label", ADD INDEX "idx_mstagty_sid_label" ("siteid", "label")',
			'ALTER TABLE "mshop_tag_type" DROP INDEX "idx_msprotaty_sid_code", ADD INDEX "idx_mstagty_sid_code" ("siteid", "code")',
		),
		'mshop_product_tag' => array(
			'RENAME TABLE "mshop_product_tag" TO "mshop_tag"',
			'ALTER TABLE "mshop_tag" DROP FOREIGN KEY "fk_msprota_typeid", ADD CONSTRAINT "fk_mstag_typeid" FOREIGN KEY ("typeid") REFERENCES "mshop_tag_type" ("id") ON UPDATE CASCADE ON DELETE CASCADE',
			'ALTER TABLE "mshop_tag" DROP INDEX "unq_msprota_sid_tid_lid_label", ADD UNIQUE INDEX "unq_mstag_sid_tid_lid_label" ("siteid", "typeid", "langid", "label")',
			'ALTER TABLE "mshop_tag" DROP INDEX "idx_msprota_sid_label", ADD INDEX "idx_mstag_sid_label" ("siteid", "label")',
			'ALTER TABLE "mshop_tag" DROP INDEX "idx_msprota_sid_langid", ADD INDEX "idx_mstag_sid_langid" ("siteid", "langid")',
		),
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductTagLangidNull', 'ProductTagTypeidAddConstraint' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
	 * Move the product tag tables
	 *
	 * @param array $stmts List of SQL statements to execute
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Moving product tag tables to own domain', 0 ); $this->status( '' );

		foreach( $stmts as $table => $stmts )
		{
			$this->msg( sprintf( 'Moving table "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true )
			{
				$this->executeList( $stmts );
				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}