<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale test data.
 */
class LocaleAddTestData extends MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return [
			'Attribute', 'Cache', 'Catalog', 'Coupon', 'Customer', 'Index', 'Job', 'Locale', 'Log', 'Media', 'Order',
			'Plugin', 'Price', 'Product', 'Review', 'Rule', 'Service', 'Stock', 'Subscription', 'Supplier', 'Tag', 'Text',
			'MShopAddLocaleLangCurData'
		];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['MShopAddLocaleData', 'MShopSetLocale'];
	}


	/**
	 * Adds locale test data.
	 */
	public function up()
	{
		$this->info( 'Adding test data for MShop locale domain', 'vv' );
		$this->context()->setEditor( 'core' );

		if( $this->context()->config()->get( 'setup/site' ) !== 'unittest' ) {
			return;
		}


		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

		if( ( $testdata = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}


		$localeManager = \Aimeos\MShop::create( $this->context(), 'locale', 'Standard' );
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
		$sites = [];
		$localeSiteManager = $localeManager->getSubManager( 'site' );
		$search = $localeSiteManager->filter()->add( ['locale.site.code' => 'unittest'] );

		foreach( $localeSiteManager->search( $search ) as $site )
		{
			$site = $localeSiteManager->getTree( $site->getId() );
			$sites = array_merge( $sites, $this->getSites( $site ) );
		}

		foreach( array_reverse( $sites ) as $site )
		{
			$this->context()->setLocale( $localeManager->create()->setSiteId( $site->getSiteId() ) );
			$localeSiteManager->delete( $site->getId() );
		}
	}
}
