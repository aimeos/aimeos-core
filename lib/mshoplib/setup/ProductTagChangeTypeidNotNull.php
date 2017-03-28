<?php

/**
* @copyright Metaways Infosystems GmbH, 2011
* @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
* @version $Id: ProductTagChangeTypeidNotNull.php 14628 2011-12-29 13:29:43Z nsendetzky $
*/


namespace Aimeos\MW\Setup\Task;


/**
 * Changes product_tag.typeid to not null.
 */
class ProductTagChangeTypeidNotNull extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'UPDATE "mshop_product_tag"
			SET "typeid" = (
				SELECT "id" FROM "mshop_product_tag_type"
				WHERE "siteid" = ( SELECT "id" FROM "mshop_locale_site" WHERE "code" = \'default\' )
				AND "domain" = \'product\' AND "code" = \'default\'
			)
			WHERE "typeid" IS NULL',
		'ALTER TABLE "mshop_product_tag" MODIFY "typeid" INTEGER NOT NULL'
	);





	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopAddTypeData', 'ProductTagAdaptColumns' );
	}



	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}



	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process( $this->mysql );
	}



	/**
	 * Modify column typeid to NOT NULL and changes NULL to value of mshop_product_tag.code="default"
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( array $stmts )
	{
		$this->msg( 'Changing typeid column in mshop_product_tag table', 0 ); $this->status( '' );

		$this->msg( sprintf( 'Checking table "%1$s": ', 'mshop_product_tag' ), 1 );

		if( $this->schema->tableExists( 'mshop_product_tag' ) === true
			&& $this->schema->columnExists( 'mshop_product_tag', 'typeid' ) === true
			&& $this->schema->getColumnDetails( 'mshop_product_tag', 'typeid' )->isNullable() === true )
		{
			$this->executeList( $stmts );
			$this->status( 'migrated' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}

}