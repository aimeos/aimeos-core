<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale records to tables.
 */
class MShopAddLocaleDataDefault extends MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddLocaleLangCurData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['MShopAddLocaleData'];
	}


	/**
	 * Adds locale data.
	 */
	public function up()
	{
		$this->info( 'Adding data for MShop locale domain', 'vv' );

		// Set editor for further tasks
		$context = $this->context()->setEditor( 'core' );
		$localeManager = \Aimeos\MShop::create( $context, 'locale', 'Standard' );

		if( !$localeManager->search( $localeManager->filter() )->isEmpty() ) {
			return;
		}

		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		$siteIds = [];

		if( isset( $data['locale/site'] ) ) {
			$siteIds = $this->addLocaleSiteData( $localeManager, $data['locale/site'] );
		}

		if( isset( $data['locale'] ) ) {
			$this->addLocaleData( $localeManager, $data['locale'], $siteIds );
		}
	}
}
