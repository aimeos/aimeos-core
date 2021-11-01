<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds performance data records to tables.
 */
class LocaleAddPerfData extends MShopAddLocaleData
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
	 * Insert records from file containing the SQL records.
	 *
	 */
	public function up()
	{
		$this->info( 'Adding performance data for MShop locale domain', 'v' );


		// Set editor for further tasks
		$this->context()->setEditor( 'unitperf:core' );


		if( $this->context()->getConfig()->get( 'setup/site' ) === 'unitperf' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

			if( ( $testdata = include( $filename ) ) == false ) {
				throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->context() );
			$localeSiteManager = $localeManager->getSubManager( 'site' );
			$siteIds = [];

			$search = $localeSiteManager->filter();
			$search->setConditions( $search->compare( '==', 'locale.site.code', 'unitperf' ) );

			foreach( $localeSiteManager->search( $search ) as $site )
			{
				$this->context()->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );
				$localeSiteManager->delete( $site->getId() );
			}

			if( isset( $testdata['locale/site'] ) ) {
				$siteIds = $this->addLocaleSiteData( $localeManager, $testdata['locale/site'] );
			}

			if( isset( $testdata['locale'] ) ) {
				$this->addLocaleData( $localeManager, $testdata['locale'], $siteIds );
			}
		}
	}
}
