<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


class CatalogIndexRenameDomainIndexes extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_index_attribute' => array(
			'idx_mscatinat_s_at_lt' => 'ALTER TABLE "mshop_index_attribute" DROP INDEX "idx_mscatinat_s_at_lt", ADD INDEX "idx_msindat_s_at_lt" ("siteid", "attrid", "listtype")',
			'idx_mscatinat_p_s_lt_t_c' => 'ALTER TABLE "mshop_index_attribute" DROP INDEX "idx_mscatinat_p_s_lt_t_c", ADD INDEX "idx_msindat_p_s_lt_t_c" ("prodid", "siteid", "listtype", "type", "code")',
		),
		'mshop_index_catalog' => array(
			'idx_mscatinca_s_ca_lt_po' => 'ALTER TABLE "mshop_index_catalog" DROP INDEX "idx_mscatinca_s_ca_lt_po", ADD INDEX "idx_msindca_s_ca_lt_po" ("siteid", "catid", "listtype", "pos")',
		),
		'mshop_index_price' => array(
			'idx_mscatinpr_s_lt_cu_ty_va' => 'ALTER TABLE "mshop_index_price" DROP INDEX "idx_mscatinpr_s_lt_cu_ty_va", ADD INDEX "idx_msindpr_s_lt_cu_ty_va" ("siteid", "listtype", "currencyid", "type", "value")',
			'idx_mscatinpr_p_s_lt_cu_ty_va' => 'ALTER TABLE "mshop_index_price" DROP INDEX "idx_mscatinpr_p_s_lt_cu_ty_va", ADD INDEX "idx_msindpr_p_s_lt_cu_ty_va" ("prodid", "siteid", "listtype", "currencyid", "type", "value")',
		),
		'mshop_index_text' => array(
			'idx_mscatinte_value' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_mscatinte_value", ADD FULLTEXT INDEX "idx_msindte_value" ("value")',
			'idx_mscatinte_sid' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_mscatinte_sid", ADD INDEX "idx_msindte_sid" ("siteid")',
			'idx_mscatinte_p_s_lt_la_ty_va' => 'ALTER TABLE "mshop_index_text" DROP INDEX "idx_mscatinte_p_s_lt_la_ty_va", ADD INDEX "idx_msindte_p_s_lt_la_ty_va" ("prodid", "siteid", "listtype", "langid", "type", "value"(16))',
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
		$this->msg( 'Renaming catalog index indexes to index', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $list )
		{
			foreach( $list as $index => $stmt )
			{
				$this->msg( sprintf( 'Checking index "%1$s"', $index ), 1 );

				if( $this->schema->tableExists( $table ) === true
					&& $this->schema->indexExists( $table, $index ) === true
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
