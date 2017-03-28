<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds locale records to tables.
 */
class MShopAddLocaleDataDefault extends \Aimeos\MW\Setup\Task\MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleLangCurData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopAddLocaleData' );
	}


	/**
	 * Adds locale data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding data for MShop locale domain', 0 );
		$this->status( '' );


		// Set editor for further tasks
		$this->additional->setEditor( 'core:setup' );


		if( $this->additional->getConfig()->get( 'setup/site', 'default' ) === 'default' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

			if( ( $data = include( $filename ) ) == false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->additional, 'Standard' );
			$siteIds = [];

			if( isset( $data['locale/site'] ) ) {
				$siteIds = $this->addLocaleSiteData( $localeManager, $data['locale/site'] );
			}

			if( isset( $data['locale'] ) ) {
				$this->addLocaleData( $localeManager, $data['locale'], $siteIds );
			}
		}
	}
}