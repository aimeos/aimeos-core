<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default records to tables.
 */
class MShopAddTypeData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	public function up()
	{
	}


	/**
	 * Adds locale data.
	 */
	protected function process( string $filename = null )
	{
		$context = $this->context();
		$sitecode = $context->getLocale()->getSiteItem()->getCode();

		$this->info( sprintf( 'Adding MShop type data for site "%1$s"', $sitecode ), 'v' );

		if( !$filename )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'type.php';
		}

		if( ( $testdata = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$this->processFile( $testdata );
	}


	protected function processFile( array $testdata )
	{
		foreach( $testdata as $domain => $datasets )
		{
			$this->info( sprintf( 'Checking "%1$s" type data', $domain ), 'v' );

			$domainManager = $this->getDomainManager( $domain );
			$type = $domainManager->create();
			$num = $total = 0;

			foreach( $datasets as $dataset )
			{
				$total++;

				$type->setId( null );
				$type->setCode( $dataset['code'] );
				$type->setDomain( $dataset['domain'] );
				$type->setLabel( $dataset['label'] );
				$type->setStatus( $dataset['status'] );

				try {
					$domainManager->save( $type );
					$num++;
				} catch( \Exception $e ) { ; } // if type was already available
			}
		}
	}


	/**
	 * Returns the manager for the given domain and sub-domains.
	 *
	 * @param string $domain String of domain and sub-domains, e.g. "product" or "order/base/service"
	 * @return \Aimeos\MShop\Common\Manager\Iface Domain manager
	 * @throws \RuntimeException If domain string is invalid or no manager can be instantiated
	 */
	protected function getDomainManager( $domain )
	{
		return \Aimeos\MShop::create( $this->context(), $domain );
	}


	/**
	 * Starts a new transaction
	 */
	protected function txBegin()
	{
		$dbm = $this->context()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction
	 */
	protected function txCommit()
	{
		$dbm = $this->context()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}
