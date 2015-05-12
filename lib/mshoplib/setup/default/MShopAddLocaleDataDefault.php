<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds locale data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding data for MShop locale domain', 0 );
		$this->_status( '' );


		// Set editor for further tasks
		$this->_additional->setEditor( 'core:setup' );


		if( $this->_additional->getConfig()->get( 'setup/site', 'default' ) === 'default' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$filename = dirname( __FILE__ ) . $ds . 'data'. $ds . 'locale.php';

			if( ( $data = include( $filename ) ) == false ) {
				throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
			}

			$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_additional, 'Default' );
			$siteIds = array();

			if( isset( $data['locale/site'] ) ) {
				$siteIds = $this->_addLocaleSiteData( $localeManager, $data['locale/site'] );
			}

			if( isset( $data['locale'] ) ) {
				$this->_addLocaleData( $localeManager, $data['locale'], $siteIds );
			}
		}
	}
}