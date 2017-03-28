<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Changes locale site table to nested set.
 */
class LocaleChangeSitesToTree extends \Aimeos\MW\Setup\Task\Base
{
	private $mysqlColumns = array(
		'level' => 'ALTER TABLE "mshop_locale_site" ADD "level" INTEGER NOT NULL',
		'nleft' => 'ALTER TABLE "mshop_locale_site" ADD "nleft" INTEGER NOT NULL',
		'nright' => 'ALTER TABLE "mshop_locale_site" ADD "nright" INTEGER NOT NULL',
	);

	private $mysqlMigrate = array(
		'insert' => '
			INSERT INTO "mshop_locale_site" ("code", "label", "config", "status", "mtime", "ctime", "editor", "level", "nleft", "nright")
			SELECT \'default\', \'Default\', \'{}\', 0, NOW(), NOW(), \'\', 0,
				( SELECT COALESCE( MAX("nright"), 0 ) + 1 FROM "mshop_locale_site" ),
				( SELECT COALESCE( MAX("nright"), 0 ) + 2 FROM "mshop_locale_site" )
			FROM DUAL
			WHERE ( SELECT COUNT(*) FROM "mshop_locale_site" WHERE "code" = \'default\' ) = 0
		',
		'search' => 'SELECT * FROM "mshop_locale_site" WHERE "code" <> \'default\' AND "nleft" = 0 AND "nright" = 0',
		'update' => 'UPDATE "mshop_locale_site" SET "level" = ?, "nleft" = ?, "nright" = ? WHERE "code" = ? AND "nleft" = 0 AND "nright" = 0',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale', 'TablesAddLogColumns' );
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
		$this->process( $this->mysqlColumns, $this->mysqlMigrate );
	}


	/**
	 * Adapts mshop_locale_site table if it exists.
	 *
	 * @param array $colstmts Associative array of table name and lists of ALTER TABLE SQL statements to execute
	 * @param array $migstmts Associative array of action and SQL statements for content migration to execute
	 */
	protected function process( array $colstmts, array $migstmts )
	{
		$migrate = false;
		$this->msg( 'Changeing locale sites to tree of sites', 0 ); $this->status( '' );

		foreach( $colstmts as $column => $stmt )
		{
			$this->msg( sprintf( 'Checking column "%1$s.%2$s": ', 'mshop_locale_site', $column ), 1 );

			if( $this->schema->tableExists( 'mshop_locale_site' ) === true
				&& $this->schema->columnExists( 'mshop_locale_site', $column ) === false )
			{
				$migrate = true;
				$this->execute( $stmt );
				$this->status( 'added' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}


		if( $migrate === true )
		{
			$this->msg( 'Migrating site items to tree structure', 1 );

			$this->conn->create( $migstmts['insert'] )->execute();

			$stmt = $this->conn->create( $migstmts['search'] );
			$result = $stmt->execute();
			$sites = [];

			while( ( $row = $result->fetch() ) !== false ) {
				$sites[] = $row;
			}

			$cnt = count( $sites );

			if( $cnt > 0 )
			{
				$stmt = $this->conn->create( $migstmts['update'] );

				foreach( $sites as $key => $site )
				{
					$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 2, ( $key + 1 ) * 2, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 3, ( $key + 1 ) * 2 + 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
					$stmt->bind( 4, $site['code'] );
					$stmt->execute()->finish();
				}

				$stmt->bind( 1, 0, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 3, ( $cnt + 1 ) * 2, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 4, 'default' );
				$stmt->execute()->finish();

				$this->status( 'done' );
			}
			else
			{
				$this->status( 'OK' );
			}
		}
	}
}
