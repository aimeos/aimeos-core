<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds locale test data.
 */
class LocaleAddTestData extends \Aimeos\MW\Setup\Task\MShopAddLocaleData
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
	 * Adds locale test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding test data for MShop locale domain', 0 );
		$this->status( '' );


		// Set editor for further tasks
		$this->additional->setEditor( 'core:lib/mshoplib' );


		if( $this->additional->getConfig()->get( 'setup/site' ) === 'unittest' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

			if( ( $testdata = include( $filename ) ) == false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional );

			$this->cleanupSites( $localeManager );

			$siteIds = [];
			if( isset( $testdata['locale/site'] ) ) {
				$siteIds = $this->addLocaleSiteData( $localeManager, $testdata['locale/site'] );
			}

			if( isset( $testdata['locale/currency'] ) ) {
				$this->addLocaleCurrencyData( $localeManager, $testdata['locale/currency'] );
			}

			if( isset( $testdata['locale/language'] ) ) {
				$this->addLocaleLanguageData( $localeManager, $testdata['locale/language'] );
			}

			if( isset( $testdata['locale'] ) ) {
				$this->addLocaleData( $localeManager, $testdata['locale'], $siteIds );
			}
		}
	}


	/**
	 * Gets recursive all sub-sites of a site sorted on their level.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Site\Iface $site Site which can contain sub-sites
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface[] $sites List with sites
	 */
	protected function getSites( \Aimeos\MShop\Locale\Item\Site\Iface $site )
	{
		$sites = [$site->getId() => $site];

		foreach( $site->getChildren() as $child ) {
			$sites += $this->getSites( $child );
		}

		return $sites;
	}


	/**
	 *
	 * Deletes old sites and their subsites.
	 *
	 * @param \Aimeos\MShop\Locale\Manager\Iface $localeManager
	 */
	protected function cleanupSites( $localeManager )
	{
		$localeSiteManager = $localeManager->getSubManager( 'site' );

		$search = $localeSiteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest' ) ) );

		$sites = [];

		foreach( $localeSiteManager->searchItems( $search ) as $site )
		{
			$site = $localeSiteManager->getTree( $site->getId() );
			$sites = array_merge( $sites, $this->getSites( $site ) );
		}

		foreach( array_reverse( $sites ) as $site )
		{
			$this->additional->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );
			$localeSiteManager->deleteItem( $site->getId() );
		}
	}
}
