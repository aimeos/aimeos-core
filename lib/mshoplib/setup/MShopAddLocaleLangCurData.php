<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds locale language and currency records to tables.
 */
class MShopAddLocaleLangCurData extends \Aimeos\MW\Setup\Task\MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TablesCreateMShop'];
	}


	/**
	 * Creates new locale data if necessary
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Add locale data for languages and currencies', 0 );
		$this->status( '' );


		$ds = DIRECTORY_SEPARATOR;
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional, 'Standard' );


		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'language.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/language'] ) ) {
			$this->addLocaleLanguageData( $localeManager, $data['locale/language'] );
		}


		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'currency.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['locale/currency'] ) ) {
			$this->addLocaleCurrencyData( $localeManager, $data['locale/currency'] );
		}
	}
}
