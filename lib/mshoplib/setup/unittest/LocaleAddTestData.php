<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds locale test data.
 */
class MW_Setup_Task_LocaleAddTestData extends MW_Setup_Task_MShopAddLocaleData
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
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds locale test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding test data for MShop locale domain', 0 );
		$this->_status( '' );


		// Set editor for further tasks
		$this->_additional->setEditor( 'core:unittest' );


		if( $this->_additional->getConfig()->get( 'setup/site' ) === 'unittest' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = dirname( __FILE__ ) . $ds . 'data' . $ds . 'locale.php';

			if( ( $testdata = include( $filename ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_additional );

			$this->_cleanupSites( $localeManager );

			$siteIds = array();
			if( isset( $testdata['locale/site'] ) ) {
				$siteIds = $this->_addLocaleSiteData( $localeManager, $testdata['locale/site'] );
			}

			if( isset( $testdata['locale/currency'] ) ) {
				$this->_addLocaleCurrencyData( $localeManager, $testdata['locale/currency'] );
			}

			if( isset( $testdata['locale/language'] ) ) {
				$this->_addLocaleLanguageData( $localeManager, $testdata['locale/language'] );
			}

			if( isset( $testdata['locale'] ) ) {
				$this->_addLocaleData( $localeManager, $testdata['locale'], $siteIds );
			}
		}
	}


	/**
	 * Gets recursive all sub-sites of a site sorted on their level.
	 *
	 * @param MShop_Locale_Item_Site_Interface $site Site which can contain sub-sites
	 * @return MShop_Locale_Item_Site_Interface[] $sites List with sites
	 */
	private function _getSites( MShop_Locale_Item_Site_Interface $site )
	{
		$sites = array( $site );

		foreach( $site->getChildren() as $child ) {
			$sites = array_merge( $sites, $this->_getSites( $child ) );
		}

		return $sites;
	}


	/**
	 *
	 * Deletes old sites and their subsites.
	 *
	 * @param MShop_Locale_Manager_Interface $localeManager
	 */
	private function _cleanupSites( $localeManager )
	{
		$localeSiteManager = $localeManager->getSubManager( 'site' );

		$search = $localeSiteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest', 'unit' ) ) );

		$sites = array();

		foreach( $localeSiteManager->searchItems( $search ) as $site )
		{
			$site = $localeSiteManager->getTree( $site->getId() );
			$sites = array_merge( $sites, $this->_getSites( $site ) );
		}

		foreach( $sites as $site )
		{
			$this->_additional->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );
			$localeSiteManager->deleteItem( $site->getId() );
		}
	}
}