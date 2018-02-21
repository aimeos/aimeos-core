<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds code records to the tables
 */
class MShopAddCodeDataDefault extends \Aimeos\MW\Setup\Task\MShopAddCodeData
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MShopAddCodeData' );
	}


	/**
	 * Returns the list of task names which depends on this task
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$site = $this->additional->getLocale()->getSite()->getCode();
		$this->msg( sprintf( 'Adding default code data for site "%1$s"', $site ), 0 ); $this->status( '' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'code.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		$this->process( $data );
	}
}