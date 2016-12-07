<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MW\Setup\Task;


/**
 * Adds parentid column to catalog and locale site table.
 */
class TreeAddParentId extends \Aimeos\MW\Setup\Task\Base
{
	private $mysql = array(
		'mshop_catalog' => array(
			'ALTER TABLE "mshop_catalog" ADD "parentid" INTEGER NOT NULL AFTER "id"',
			'UPDATE mshop_catalog mc1 SET parentid = (
				SELECT id FROM
					( SELECT * FROM mshop_catalog )
				AS mc2
				WHERE
					mc2.siteid = mc1.siteid AND
					mc2.nleft < mc1.nleft AND
					mc2.nright > mc1.nright AND
					mc2.level = mc1.level - 1
				LIMIT 1
			) WHERE parentid = 0'
		),

		'mshop_locale_site' => array(
			'ALTER TABLE "mshop_locale_site" ADD "parentid" INTEGER NOT NULL AFTER "id"',
			'UPDATE mshop_locale_site ml1 SET parentid = (
				SELECT id FROM
					( SELECT * FROM mshop_locale_site )
				AS ml2
				WHERE
					ml2.nleft < ml1.nleft AND
					ml2.nright > ml1.nright AND
					ml2.level = ml1.level - 1
				LIMIT 1
			) WHERE parentid = 0'
		)
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogTreeToCatalog' );
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
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function process( $stmts )
	{
		$this->msg( 'Adding parentid column to catalog and locale_site', 0 );
		$this->status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->msg( sprintf( 'Checking parentid column in "%1$s"', $table ), 1 );

			if( $this->schema->tableExists( $table ) === true && $this->schema->columnExists( $table, 'parentid' ) === false )
			{
				$this->executeList( $stmt );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}