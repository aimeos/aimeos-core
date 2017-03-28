<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds default records to tables.
 */
class MShopAddTypeData extends \Aimeos\MW\Setup\Task\Base
{
	private $editor = '';
	private $domainManagers = [];


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
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		// executed by tasks in sub-directories for specific sites
	}


	/**
	 * Adds locale data.
	 */
	protected function process()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$sitecode = $this->additional->getLocale()->getSite()->getCode();
		$this->msg( sprintf( 'Adding MShop type data for site "%1$s"', $sitecode ), 0 );
		$this->status( '' );


		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'type.php';

		if( ( $testdata = include( $filename ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$this->processFile( $testdata );
	}


	protected function processFile( array $testdata )
	{
		$editor = $this->additional->getEditor();
		$this->additional->setEditor( $this->editor );


		foreach( $testdata as $domain => $datasets )
		{
			$this->msg( sprintf( 'Checking "%1$s" type data', $domain ), 1 );

			$domainManager = $this->getDomainManager( $domain );
			$type = $domainManager->createItem();
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
					$domainManager->saveItem( $type );
					$num++;
				} catch( \Exception $e ) { ; } // if type was already available
			}

			$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
		}

		$this->additional->setEditor( $editor );
	}


	/**
	 * Returns the manager for the given domain and sub-domains.
	 *
	 * @param string $domain String of domain and sub-domains, e.g. "product" or "order/base/service"
	 * @return \Aimeos\MShop\Common\Manager\Iface Domain manager
	 * @throws \Aimeos\Controller\Frontend\Exception If domain string is invalid or no manager can be instantiated
	 */
	protected function getDomainManager( $domain )
	{
		$domain = strtolower( trim( $domain, "/ \n\t\r\0\x0B" ) );

		if( strlen( $domain ) === 0 ) {
			throw new \RuntimeException( 'An empty domain is invalid' );
		}

		if( !isset( $this->domainManagers[$domain] ) )
		{
			$parts = explode( '/', $domain );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \RuntimeException( sprintf( 'Invalid domain "%1$s"', $domain ) );
				}
			}

			if( ( $domainname = array_shift( $parts ) ) === null ) {
				throw new \RuntimeException( 'An empty domain is invalid' );
			}


			if( !isset( $this->domainManagers[$domainname] ) )
			{
				$iface = '\\Aimeos\\MShop\\Common\\Manager\\Iface';
				$factory = '\\Aimeos\\MShop\\' . ucwords( $domainname ) . '\\Manager\\Factory';
				$manager = call_user_func_array( $factory . '::createManager', array( $this->additional ) );

				if( !( $manager instanceof $iface ) ) {
					throw new \RuntimeException( sprintf( 'No factory "%1$s" found', $factory ) );
				}

				$this->domainManagers[$domainname] = $manager;
			}


			foreach( $parts as $part )
			{
				$tmpname = $domainname . '/' . $part;

				if( !isset( $this->domainManagers[$tmpname] ) ) {
					$this->domainManagers[$tmpname] = $this->domainManagers[$domainname]->getSubManager( $part );
				}

				$domainname = $tmpname;
			}
		}

		return $this->domainManagers[$domain];
	}


	/**
	 * Starts a new transaction
	 */
	protected function txBegin()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction
	 */
	protected function txCommit()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}