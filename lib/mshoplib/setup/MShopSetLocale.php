<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Sets locale in context.
 */
class MShopSetLocale extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopAddTypeData' );
	}


	/**
	 * Adds locale data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$site = $this->additional->getConfig()->get( 'setup/site', 'default' );


		$this->msg( sprintf( 'Setting locale to "%1$s"', $site ), 0 );

		// Set locale for further tasks
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->additional, 'Standard' );
		$this->additional->setLocale( $localeManager->bootstrap( $site, '', '', false ) );

		$this->status( 'OK' );
	}
}