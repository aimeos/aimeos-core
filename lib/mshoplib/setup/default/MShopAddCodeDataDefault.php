<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale', 'MShopAddCodeData'];
	}


	/**
	 * Executes the task for MySQL databases
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$site = $this->additional->getLocale()->getSiteItem()->getCode();
		$this->msg( sprintf( 'Adding default code data for site "%1$s"', $site ), 0 ); $this->status( '' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'code.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		$this->process( $data );
	}
}
