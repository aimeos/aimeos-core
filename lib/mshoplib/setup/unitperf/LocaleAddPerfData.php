<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TablesAddMShopPerfData.php 14535 2011-12-21 16:47:21Z nsendetzky $
 */


/**
 * Adds performance data records to tables.
 */
class MW_Setup_Task_LocaleAddPerfData extends MW_Setup_Task_MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopSetLocale' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert records from file containing the SQL records.
	 *
	 * @param string $filename Name of the file
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding performance data for MShop locale domain', 0 );
		$this->_status( '' );


		// Set editor for further tasks
		$this->_additional->setEditor( 'unitperf:core' );


		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __FILE__ ) . $ds . 'data' . $ds . 'locale.php';

		if( ( $testdata = include( $filename ) ) == false ) {
			throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}


		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_additional );
		$localeSiteManager = $localeManager->getSubManager( 'site' );
		$siteIds = array();


		$search = $localeSiteManager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unitperf' ) );

		foreach( $localeSiteManager->searchItems( $search ) as $site )
		{
			try
			{
				$this->_additional->setLocale( $localeManager->bootstrap( $site->getCode(), '', '', false ) );

				$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional, 'Default' );
				$catalogIndexManager = $catalogManager->getSubManager( 'index', 'Default' );

				$search = $catalogIndexManager->createSearch();

				foreach( $catalogIndexManager->searchItems( $search ) as $item ) {
					$catalogIndexManager->deleteItem( $item->getId() );
				}
			}
			catch( Exception $e )
			{
				$this->_msg( 'Error: No locale available for cleaning up catalog index', 1 );
				$this->_status( 'warn' );
			}

			$localeSiteManager->deleteItem( $site->getId() );
		}


		if( isset( $testdata['locale/site'] ) ) {
			$siteIds = $this->_addLocaleSiteData( $localeManager, $testdata['locale/site'] );
		}

		if( isset( $testdata['locale'] ) ) {
			$this->_addLocaleData( $localeManager, $testdata['locale'], $siteIds );
		}
	}
}