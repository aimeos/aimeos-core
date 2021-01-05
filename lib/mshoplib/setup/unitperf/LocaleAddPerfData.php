<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance data records to tables.
 */
class LocaleAddPerfData extends \Aimeos\MW\Setup\Task\MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopAddLocaleLangCurData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['MShopAddLocaleData'];
	}


	/**
	 * Insert records from file containing the SQL records.
	 *
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding performance data for MShop locale domain', 0 );
		$this->status( '' );


		// Set editor for further tasks
		$this->additional->setEditor( 'unitperf:core' );


		if( $this->additional->getConfig()->get( 'setup/site' ) === 'unitperf' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

			if( ( $testdata = include( $filename ) ) == false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional );
			$localeSiteManager = $localeManager->getSubManager( 'site' );
			$siteIds = [];

			$search = $localeSiteManager->filter();
			$search->setConditions( $search->compare( '==', 'locale.site.code', 'unitperf' ) );

			foreach( $localeSiteManager->search( $search ) as $site )
			{
				$this->additional->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );
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
