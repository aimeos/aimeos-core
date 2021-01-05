<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getPreDependencies() : array
	{
		return ['MShopAddLocaleData'];
	}


	/**
	 * Adds locale data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$site = $this->additional->getConfig()->get( 'setup/site', 'default' );


		$this->msg( sprintf( 'Setting locale to "%1$s"', $site ), 0 );

		// Set locale for further tasks
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional, 'Standard' );
		$locale = $localeManager->bootstrap( $site, '', '', false )->setLanguageId( null )->setCurrencyId( null );
		$this->additional->setLocale( $locale );

		$this->status( 'OK' );
	}
}
