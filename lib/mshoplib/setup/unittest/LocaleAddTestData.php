<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: LocaleAddTestData.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Adds locale test data.
 */
class MW_Setup_Task_LocaleAddTestData extends MW_Setup_Task_MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
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
		$this->_additional->setEditor( 'core:unittest' );
		$this->_status( '' );


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

		// Set locale for further tasks
		$this->_additional->setLocale( $localeManager->bootstrap( 'unittest', '', '', false ) );
		$this->_additional->setEditor( 'core:unittest' );
	}


	/**
	 * Gets recursive all sub-sites of a site sorted on their level.
	 *
	 * @param MShop_Locale_Item_Site_Interface $site Site which can contain sub-sites
	 * @param array $sites List with sites
	 * @return array $sites List with sites
	 */
	private function _getCodes( MShop_Locale_Item_Site_Interface $site )
	{
		$sites = array();
		foreach( $site->getChildren() as $child )
		{
			$leafSites = $this->_getCodes( $child );
			$sites = array_merge( $leafSites, $sites );
		}

		return $sites;
	}


	/**
	 *
	 * Deletes old sites and their subsites.
	 *
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
			$sites = array_merge( $sites, $this->_getCodes( $site ) );
			$sites[ $site->getCode() ] = $site;
		}

		foreach( $sites as $site )
		{

			$this->_additional->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );

			$orderBaseManager = MShop_Order_Manager_Factory::createManager( $this->_additional )->getSubManager( 'base' );

			$search = $orderBaseManager->createSearch();
			$search->setConditions( $search->compare( '==', 'order.base.sitecode', $site->getCode() ) );

			$orders = $orderBaseManager->searchItems( $search );

			foreach( $orders as $order ) {
				$orderBaseManager->deleteItem( $order->getId() );
			}

			$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional );
			$catalogIndexManager = $catalogManager->getSubManager( 'index' );

			$search = $catalogIndexManager->createSearch();

			foreach( $catalogIndexManager->searchItems( $search ) as $item ) {
				try{
					$catalogIndexManager->deleteItem( $item->getId() );
				} catch( Exception $e) { ; }
			}

			$localeSiteManager->deleteItem( $site->getId() );
		}
	}
}