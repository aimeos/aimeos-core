<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


class CatalogIndexRenameDomainConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_index_attribute' => array(
			'unq_mscatinat_p_s_aid_lt' => 'ALTER TABLE "mshop_index_attribute" DROP INDEX "unq_mscatinat_p_s_aid_lt", ADD UNIQUE INDEX "unq_msindat_p_s_aid_lt" ("prodid", "siteid", "attrid", "listtype")',
		),
		'mshop_index_catalog' => array(
			'unq_mscatinca_p_s_cid_lt_po' => 'ALTER TABLE "mshop_index_catalog" DROP INDEX "unq_mscatinca_p_s_cid_lt_po", ADD UNIQUE INDEX "unq_msindca_p_s_cid_lt_po" ("prodid", "siteid", "catid", "listtype", "pos")',
		),
		'mshop_index_price' => array(
			'unq_mscatinpr_p_s_prid_lt' => 'ALTER TABLE "mshop_index_price" DROP INDEX "unq_mscatinpr_p_s_prid_lt", ADD UNIQUE INDEX "unq_msindpr_p_s_prid_lt" ("prodid", "siteid", "priceid", "listtype")',
		),
		'mshop_index_text' => array(
			'unq_mscatinte_p_s_tid_lt' => 'ALTER TABLE "mshop_index_text" DROP INDEX "unq_mscatinte_p_s_tid_lt", ADD UNIQUE INDEX "unq_msindte_p_s_tid_lt" ("prodid", "siteid", "textid", "listtype")',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogIndexRenameDomainTables' );
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
	 * Rename catalog index indexes to index
	 *
	 * @param array $stmts List of SQL statements to execute for renaming
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Renaming catalog index constraints to index', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $list )
		{
			foreach( $list as $constraint => $stmt )
			{
				$this->msg( sprintf( 'Checking constraint "%1$s"', $constraint ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->constraintExists( $table, $constraint ) === true
				) {
					$this->execute( $stmt );
					$this->status( 'done' );
				}
				else
				{
					$this->status( 'OK' );
				}
			}
		}
	}
}
