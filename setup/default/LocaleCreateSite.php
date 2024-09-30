<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale records to tables.
 */
class LocaleCreateSite extends MShopAddLocaleData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddLocaleLangCurData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['MShopAddLocaleDataDefault'];
	}


	/**
	 * Adds locale data.
	 */
	public function up()
	{
		$this->info( 'Create site and locale', 'vv' );

		$context = $this->context()->setEditor( 'core' ); // Set editor for further tasks
		$localeManager = \Aimeos\MShop::create( $context, 'locale', 'Standard' );

		$config = $context->config();
		$site = $config->get( 'setup/site', 'default' );
		$lang = $config->get( 'setup/language', 'en' );
		$curr = $config->get( 'setup/currency', 'USD' );

		$siteIds = $this->addLocaleSiteData( $localeManager, [
			$site => ['locale.site.code' => $site, 'locale.site.label' => ucfirst( $site )]
		] );

		$this->addLocaleData( $localeManager, [
			['site' => $site, 'locale.languageid' => $lang, 'locale.currencyid' => $curr]
		], $siteIds );
	}
}
