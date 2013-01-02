<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogAddCode.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */

/**
 * Adds parentid column to catalog and locale site table.
 */
class MW_Setup_Task_CatalogAddParentId extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_catalog' => 'ALTER TABLE "mshop_catalog" ADD "parentid" INTEGER NOT NULL AFTER "id"',
		'mshop_locale_site' => 'ALTER TABLE "mshop_locale_site" ADD "parentid" INTEGER NOT NULL AFTER "id"'
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogTreeToCatalog' );
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
		$this->_process( $this->_mysql );
	}


	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding parentid column to catalog and locale_site', 0 );

		foreach( $this->_mysql as $table => $stmt )
		{
			$msg = sprintf( 'Checking parentid column in "%1$s"', $table );
			$this->_msg( $msg, 1 );
			if( $this->_schema->tableExists( $table ) === true	&& $this->_schema->columnExists( $table, 'parentid' ) === false )
			{
				$this->_execute( $stmt );
				$this->_choseSiteId( $table );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}


	private function _choseSiteId( $table )
	{
		$sql = 'SELECT * from ' . $table;

		$stmt = $this->_conn->create( $sql );
		$results = $stmt->execute();

		$trees = array();

		while( ( $row = $results->fetch() ) !== false )	{
			$trees[ $row[ 'siteid' ] ][ $row[ 'level' ] ][ $row[ 'id' ] ] = $row;
		}

		foreach( $trees as $siteid => $tree ) {
			$this->_setParentId( $table, $tree, $siteid );
		}
	}

	private function _setParentId( $table, $nodes = array(), $siteid = null )
	{
		$maxLvl = count( $nodes );
		$ids = array();

		for( $lvl = 0; $lvl < $maxLvl; $lvl++ )
		{
			foreach( $nodes[ $lvl ] as $nid => $values )
			{
				if( $values[ 'level' ] === '0' ) {
					$ids[ $values[ 'id' ] ] = '0';
				}

				if( isset( $nodes[ $lvl + 1 ] ) )
				{
					foreach( $nodes[ $lvl + 1 ] as $id => $value )
					{
						foreach( $nodes[ $lvl ] as $parentid => $parentvalue )
						{
							if( $value[ 'nleft' ] > $parentvalue[ 'nleft' ] && $value[ 'nright' ] < $parentvalue[ 'nright' ] ) {
								$ids[ $id ] = $parentvalue[ 'id' ];
							}
						}
					}
				}
			}
		}

		if( $table === 'mshop_catalog' ) {
			$updatesql = 'UPDATE mshop_catalog SET "parentid" = ? WHERE siteid = ? and id = ?';

			foreach( $ids as $id => $parentid )
			{
				$stmt = null;
				$stmt = $this->_conn->create( $updatesql );
				$stmt->bind( 1, $parentid );
				$stmt->bind( 2, $siteid );
				$stmt->bind( 3, $id );
				$result = $stmt->execute()->finish();
			}
		}
		else
		{
			$updatesql = 'UPDATE mshop_locale_site SET "parentid" = ? WHERE id = ?';

			foreach( $ids as $id => $parentid )
			{
				$stmt = null;
				$stmt = $this->_conn->create( $updatesql );
				$stmt->bind( 1, $parentid );
				$stmt->bind( 2, $id );
				$result = $stmt->execute()->finish();
			}
		}
	}
}