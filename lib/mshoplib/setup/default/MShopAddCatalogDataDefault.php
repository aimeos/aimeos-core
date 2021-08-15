<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to catalog tables.
 */
class MShopAddCatalogDataDefault extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Insert catalog nodes and relations.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$sitecode = $this->additional->getLocale()->getSiteItem()->getCode();
		$this->msg( sprintf( 'Adding MShop catalog data for site "%1$s"', $sitecode ), 0, '' );

		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'catalog.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$manager = \Aimeos\MShop::create( $this->additional, 'catalog' );

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
