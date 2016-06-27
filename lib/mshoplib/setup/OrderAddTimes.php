<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the time values in order tables
 */
class OrderAddTimes extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderRenameTables' );
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
	 * Migrate database schema
	 */
	public function migrate()
	{
		$dbdomain = 'db-order';
		$this->msg( 'Adding time columns to order table', 0 );

		$dbal = $this->getConnection( $dbdomain )->getRawObject();

		if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
			throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
		}


		$fromSchema = $dbal->getSchemaManager()->createSchema();
		$toSchema = clone $fromSchema;

		$this->addIndexes( $this->addColumns( $toSchema->getTable( 'mshop_order' ) ) );
		$sql = $fromSchema->getMigrateToSql( $toSchema, $dbal->getDatabasePlatform() );

		if( $sql !== array() )
		{
			$this->executeList( $sql, $dbdomain );
			$this->migrateData( $dbdomain );
			$this->status( 'done' );
		}
		else
		{
			$this->status( 'OK' );
		}
	}


	/**
	 * Adds the missing columns to the table
	 *
	 * @param \Doctrine\DBAL\Schema\Table $table Table object
	 * @return \Doctrine\DBAL\Schema\Table Updated table object
	 */
	protected function addColumns( \Doctrine\DBAL\Schema\Table $table )
	{
		$columns = array(
			'cdate' => array( 'string', array( 'fixed' => 10 ) ),
			'cmonth' => array( 'string', array( 'fixed' => 7 ) ),
			'chour' => array( 'string', array( 'fixed' => 2 ) ),
		);

		foreach( $columns as $name => $def )
		{
			if( $table->hasColumn( $name ) === false ) {
				$table->addColumn( $name, $def[0], $def[1] );
			}
		}

		return $table;
	}


	/**
	 * Adds the missing indexes to the table
	 *
	 * @param \Doctrine\DBAL\Schema\Table $table Table object
	 * @return \Doctrine\DBAL\Schema\Table Updated table object
	 */
	protected function addIndexes( \Doctrine\DBAL\Schema\Table $table )
	{
		$indexes = array(
			'idx_msord_sid_cdate' => array( 'siteid', 'cdate' ),
			'idx_msord_sid_cmonth' => array( 'siteid', 'cmonth' ),
			'idx_msord_sid_hour' => array( 'siteid', 'chour' ),
		);

		foreach( $indexes as $name => $def )
		{
			if( $table->hasIndex( $name ) === false ) {
				$table->addIndex( $def, $name );
			}
		}

		return $table;
	}


	/**
	 * Migrates the time values
	 *
	 * @param string $dbdomain Database domain
	 */
	protected function migrateData( $dbdomain )
	{
		$start = 0;
		$conn = $this->getConnection( $dbdomain );
		$select = 'SELECT "id", "ctime" FROM "mshop_order" WHERE "cdate" = \'\' LIMIT 1000 OFFSET :offset';
		$update = 'UPDATE "mshop_order" SET "cdate" = ?, "cmonth" = ?, "chour" = ? WHERE "id" = ?';

		$stmt = $conn->create( $update, \Aimeos\MW\DB\Connection\Base::TYPE_PREP );

		do
		{
			$count = 0;
			$map = array();
			$sql = str_replace( ':offset', $start, $select );
			$result = $conn->create( $sql )->execute();

			while( ( $row = $result->fetch() ) !== false )
			{
				$map[$row['id']] = $row['ctime'];
				$count++;
			}

			foreach( $map as $id => $ctime )
			{
				list( $date, $time ) = explode( ' ', $ctime );

				$stmt->bind( 1, $date );
				$stmt->bind( 2, substr( $date, 0, 7 ) );
				$stmt->bind( 3, substr( $time, 0, 2 ) );
				$stmt->bind( 4, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}

			$start += $count;
		}
		while( $count === 1000 );
	}
}
