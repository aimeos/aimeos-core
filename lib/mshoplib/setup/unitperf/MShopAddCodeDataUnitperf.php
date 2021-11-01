<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default codes to tables
 */
class MShopAddCodeDataUnitperf extends MShopAddCodeData
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Executes the task for MySQL databases
	 */
	public function up()
	{
		$site = $this->context()->getLocale()->getSiteItem()->getCode();
		$this->info( sprintf( 'Adding default code data for site "%1$s"', $site ), 'v' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . '..' . $ds . 'default' . $ds . 'data' . $ds . 'code.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		$this->process( $data );
	}
}
