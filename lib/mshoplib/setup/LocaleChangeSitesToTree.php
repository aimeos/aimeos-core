<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Changes locale site table to nested set.
 */
class MW_Setup_Task_LocaleChangeSitesToTree extends MW_Setup_Task_Abstract
{
	private $_mysqlColumns = array(
		'level' => 'ALTER TABLE "mshop_locale_site" ADD "level" INTEGER NOT NULL',
		'nleft' => 'ALTER TABLE "mshop_locale_site" ADD "nleft" INTEGER NOT NULL',
		'nright' => 'ALTER TABLE "mshop_locale_site" ADD "nright" INTEGER NOT NULL',
	);

	private $_mysqlMigrate = array(
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
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'GlobalMoveTablesToLocale', 'TablesAddLogColumns' );
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
	protected function _mysql()
	{
		$this->_process( $this->_mysqlColumns, $this->_mysqlMigrate );
	}


	/**
	 * Adapts mshop_locale_site table if it exists.
	 *
	 * @param array $stmts Associative array of table name and lists of SQL statements to execute.
	 */
	protected function _process( array $colstmts, array $migstmts )
	{
		$migrate = false;
		$this->_msg( 'Changeing locale sites to tree of sites', 0 ); $this->_status( '' );

		foreach( $colstmts as $column => $stmt )
		{
			$this->_msg( sprintf( 'Checking column "%1$s.%2$s": ', 'mshop_locale_site', $column ), 1 );

			if( $this->_schema->tableExists( 'mshop_locale_site' ) === true
				&& $this->_schema->columnExists( 'mshop_locale_site', $column ) === false )
			{
				$migrate = true;
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}


		if( $migrate === true )
		{
			$this->_msg( 'Migrating site items to tree structure', 1 );

			$stmt = $this->_conn->create( $migstmts['insert'] )->execute();

			$stmt = $this->_conn->create( $migstmts['search'] );
			$result = $stmt->execute();
			$sites = array();

			while( ( $row = $result->fetch() ) !== false ) {
				$sites[] = $row;
			}

			$cnt = count( $sites );

			if( $cnt > 0 )
			{
				$stmt = $this->_conn->create( $migstmts['update'] );

				foreach( $sites as $key => $site )
				{
					$stmt->bind( 1, 1, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 2, ( $key + 1 ) * 2, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 3, ( $key + 1 ) * 2 + 1, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 4, $site['code'] );
					$stmt->execute()->finish();
				}

				$stmt->bind( 1, 0, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 2, 1, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 3, ( $cnt + 1 ) * 2, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 4, 'default' );
				$stmt->execute()->finish();

				$this->_status( 'done' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
