<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all required tables.
 */
class TablesCreateMAdmin extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Creates the MAdmin tables
	 */
	public function migrate()
	{
		$this->msg( 'Creating admin tables', 0 );
		$this->status( '' );

		$ds = DIRECTORY_SEPARATOR;

		$files = array(
			'db-cache' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'cache.php',
			'db-log' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'log.php',
			'db-job' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'job.php',
			'db-queue' => realpath( __DIR__ ) . $ds . 'default' . $ds . 'schema' . $ds . 'queue.php',
		);

		$this->setup( $files );
	}


	/**
	 * Creates all required tables if they doesn't exist
	 */
	protected function setup( array $files )
	{
		foreach( $files as $rname => $filepath )
		{
			$this->msg( 'Using schema from ' . basename( $filepath ), 1 ); $this->status( '' );

			if( ( $list = include( $filepath ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Unable to get list from file "%1$s"', $filepath ) );
			}

			$dbal = $this->getConnection( $rname )->getRawObject();

			if( !( $dbal instanceof \Doctrine\DBAL\Connection ) ) {
				throw new \Aimeos\MW\Setup\Exception( 'Not a DBAL connection' );
			}

			$dbalschema = new \Doctrine\DBAL\Schema\Schema();;
			$platform = $dbal->getDatabasePlatform();
			$schema = $this->getSchema( $rname );

			if( isset( $list['table'] ) )
			{
				foreach( (array) $list['table'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking table "%1$s": ', $name ), 2 );

					if( $schema->tableExists( $name ) !== true ) {
						$this->executeList( $fcn( clone $dbalschema )->toSql( $platform ), $rname );
						$this->status( 'created' );
					} else {
						$this->status( 'OK' );
					}
				}
			}

			if( isset( $list['sequence'] ) )
			{
				foreach( (array) $list['sequence'] as $name => $fcn )
				{
					$this->msg( sprintf( 'Checking sequence "%1$s": ', $name ), 2 );

					if( $schema->supports( $schema::HAS_SEQUENCES ) && $schema->sequenceExists( $name ) !== true ) {
						$this->executeList( $fcn( clone $dbalschema )->toSql( $platform ), $rname );
						$this->status( 'created' );
					} else {
						$this->status( 'OK' );
					}
				}
			}
		}
	}
}
