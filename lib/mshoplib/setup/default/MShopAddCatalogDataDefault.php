<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to catalog tables.
 */
class MShopAddCatalogDataDefault extends Base
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


	/**
	 * Insert catalog nodes and relations.
	 */
	public function up()
	{
		$sitecode = $this->context()->getLocale()->getSiteItem()->getCode();
		$this->info( sprintf( 'Adding MShop catalog data for site "%1$s"', $sitecode ), 'v' );

		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'catalog.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'catalog' );

		foreach( $data as $entry )
		{
			try {
				$manager->find( $entry['catalog.code'] );
			} catch( \Aimeos\MW\Exception $e ) {
				$manager->insert( $manager->create()->fromArray( $entry ) );
			}
		}
	}
}
