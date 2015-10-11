<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Removes constraints from index tables.
 */
class CatalogDropIndexLocaleConstraints extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog_index_attribute' => array(
			'fk_mscatinat_prodid' => 'ALTER TABLE "mshop_catalog_index_attribute" DROP FOREIGN KEY "fk_mscatinat_prodid"',
			'fk_mscatinat_siteid' => 'ALTER TABLE "mshop_catalog_index_attribute" DROP FOREIGN KEY "fk_mscatinat_siteid"',
			'fk_mscatinat_attrid' => 'ALTER TABLE "mshop_catalog_index_attribute" DROP FOREIGN KEY "fk_mscatinat_attrid"',
		),
		'mshop_catalog_index_catalog' => array(
			'fk_mscatinca_prodid' => 'ALTER TABLE "mshop_catalog_index_catalog" DROP FOREIGN KEY "fk_mscatinca_prodid"',
			'fk_mscatinca_siteid' => 'ALTER TABLE "mshop_catalog_index_catalog" DROP FOREIGN KEY "fk_mscatinca_siteid"',
			'fk_mscatinca_catid' => 'ALTER TABLE "mshop_catalog_index_catalog" DROP FOREIGN KEY "fk_mscatinca_catid"',
		),
		'mshop_catalog_index_price' => array(
			'fk_mscatinpr_prodid' => 'ALTER TABLE "mshop_catalog_index_price" DROP FOREIGN KEY "fk_mscatinpr_prodid"',
			'fk_mscatinpr_siteid' => 'ALTER TABLE "mshop_catalog_index_price" DROP FOREIGN KEY "fk_mscatinpr_siteid"',
			'fk_mscatinpr_priceid' => 'ALTER TABLE "mshop_catalog_index_price" DROP FOREIGN KEY "fk_mscatinpr_priceid"',
			'fk_mscatinpr_curid' => 'ALTER TABLE "mshop_catalog_index_price" DROP FOREIGN KEY "fk_mscatinpr_curid"',
		),
		'mshop_catalog_index_text' => array(
			'fk_mscatinte_prodid' => 'ALTER TABLE "mshop_catalog_index_text" DROP FOREIGN KEY "fk_mscatinte_prodid"',
			'fk_mscatinte_siteid' => 'ALTER TABLE "mshop_catalog_index_text" DROP FOREIGN KEY "fk_mscatinte_siteid"',
			'fk_mscatinte_textid' => 'ALTER TABLE "mshop_catalog_index_text" DROP FOREIGN KEY "fk_mscatinte_textid"',
			'fk_mscatinte_langid' => 'ALTER TABLE "mshop_catalog_index_text" DROP FOREIGN KEY "fk_mscatinte_langid"',
		),
	);




	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddIndexPriceidTextid' );
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
		$this->msg( 'Removing constraints from index tables', 0 );
		$this->status( '' );

		$schema = $this->getSchema( 'db-product' );

		foreach( $stmts as $table => $list )
		{
			if( $schema->tableExists( $table ) === true )
			{
				foreach( $list as $constraint => $stmt )
				{
					$this->msg( sprintf( 'Removing "%1$s" from "%2$s"', $constraint, $table ), 1 );

					if( $schema->constraintExists( $table, $constraint ) !== false )
					{
						$this->execute( $stmt, 'db-product' );
						$this->status( 'done' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}