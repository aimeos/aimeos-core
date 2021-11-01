<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale language and currency records to tables.
 */
class MShopAddLocaleLangCurData extends MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Locale'];
	}


	/**
	 * Creates new locale data if necessary
	 */
	public function up()
	{
		$this->info( 'Add locale data for languages and currencies', 'v' );

		$ds = DIRECTORY_SEPARATOR;
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->context(), 'Standard' );


		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'language.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/language'] ) ) {
			$this->addLocaleLanguageData( $localeManager, $data['locale/language'] );
		}


		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'currency.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/currency'] ) ) {
			$this->addLocaleCurrencyData( $localeManager, $data['locale/currency'] );
		}
	}
}
