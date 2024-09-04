<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Sets locale in context.
 */
class MShopSetLocale extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Log', 'MShopAddLocaleData'];
	}


	/**
	 * Adds locale data.
	 */
	public function up()
	{
		$context = $this->context();
		$site = $context->config()->get( 'setup/site', 'default' );

		$this->info( sprintf( 'Setting locale to "%1$s"', $site ), 'vv' );

		// Set locale for further tasks
		$localeManager = \Aimeos\MShop::create( $context, 'locale', 'Standard' );
		$locale = $localeManager->bootstrap( $site, '', '', false )->setLanguageId( null )->setCurrencyId( null );
		$context->setLocale( $locale );
	}
}
