<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2022
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
		$sitecode = $context->locale()->getSiteItem()->getCode();

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


	protected function processFile( array $data )
	{
		foreach( $data as $domain => $entries )
		{
			$this->info( sprintf( 'Checking "%1$s" type data', $domain ), 'v' );

			$manager = $this->getDomainManager( $domain );
			$prefix = str_replace( '/', '.', $domain ) . '.';
			$filter = $manager->filter();
			$expr = $map = [];

			foreach( $entries as $entry )
			{
				$expr[] = $filter->and( [
					$filter->is( $prefix . 'domain', '==', $entry['domain'] ),
					$filter->is( $prefix . 'code', '==', $entry['code'] )
				] );
			}

			foreach( $manager->search( $filter->add( $filter->and( $expr ) ) ) as $id => $item ) {
				$map[$item->getDomain()][$item->getCode()][$id] = $item;
			}

			foreach( $entries as $entry )
			{
				if( isset( $map[$entry['domain']][$entry['code']] ) ) {
					continue;
				}

				$item = $manager->create()
					->setCode( $entry['code'] )
					->setDomain( $entry['domain'] )
					->setLabel( $entry['label'] )
					->setStatus( $entry['status'] );

				$manager->save( $item );
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
		$dbm = $this->context()->db();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction
	 */
	protected function txCommit()
	{
		$dbm = $this->context()->db();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}
