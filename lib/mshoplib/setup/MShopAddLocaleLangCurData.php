<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Adds locale language and currency records to tables.
 */
class MW_Setup_Task_MShopAddLocaleLangCurData extends MW_Setup_Task_MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
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
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Creates new locale data if necessary
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Add locale data for languages and currencies', 0 );
		$this->status( '' );


		$ds = DIRECTORY_SEPARATOR;
		$localeManager = MShop_Locale_Manager_Factory::createManager( $this->additional, 'Default' );


		$filename = dirname( __FILE__ ) . $ds . 'default'.  $ds . 'data'. $ds . 'language.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/language'] ) ) {
			$this->addLocaleLanguageData( $localeManager, $data['locale/language'] );
		}


		$filename = dirname( __FILE__ ) . $ds . 'default'.  $ds . 'data'. $ds . 'currency.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/currency'] ) ) {
			$this->addLocaleCurrencyData( $localeManager, $data['locale/currency'] );
		}
	}
}