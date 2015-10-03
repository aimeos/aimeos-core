<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds locale records to tables.
 */
class MW_Setup_Task_MShopAddLocaleDataDefault extends MW_Setup_Task_MShopAddLocaleData
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
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds locale data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding data for MShop locale domain', 0 );
		$this->status( '' );


		// Set editor for further tasks
		$this->additional->setEditor( 'core:setup' );


		if( $this->additional->getConfig()->get( 'setup/site', 'default' ) === 'default' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = dirname( __FILE__ ) . $ds . 'data' . $ds . 'locale.php';

			if( ( $data = include( $filename ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = MShop_Locale_Manager_Factory::createManager( $this->additional, 'Default' );
			$siteIds = array();

			if( isset( $data['locale/site'] ) ) {
				$siteIds = $this->addLocaleSiteData( $localeManager, $data['locale/site'] );
			}

			if( isset( $data['locale'] ) ) {
				$this->addLocaleData( $localeManager, $data['locale'], $siteIds );
			}
		}
	}
}