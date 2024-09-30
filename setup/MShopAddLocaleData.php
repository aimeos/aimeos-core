<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale records to tables.
 */
class MShopAddLocaleData extends Base
{
	public function up()
	{
	}


	/**
	 * Adds locale site data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param array $data Associative list of locale site data
	 * @param string $manager Manager implementation name
	 * @param string|null $parentId Parent id of the locale item
	 * @return array Associative list of keys from the data and generated site ID
	 */
	protected function addLocaleSiteData( \Aimeos\MShop\Common\Manager\Iface $localeManager, array $data, $manager = 'Standard', $parentId = null )
	{
		$this->info( 'Adding data for MShop locale sites', 'vv', 1 );

		$siteIds = [];
		$manager = $localeManager->getSubManager( 'site', $manager );

		foreach( $data as $key => $dataset )
		{
			try
			{
				$item = $manager->find( $key );
			}
			catch( \Aimeos\MShop\Exception $e )
			{
				$manager->begin();
				$item = $manager->insert( $manager->create()->fromArray( $dataset ), $parentId );
				$manager->commit();
			}

			$siteIds[$key] = ['id' => $item->getId(), 'site' => $item->getSiteId()];
		}

		return $siteIds;
	}


	/**
	 * Adds locale currency data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param array $data Associative list of locale currency data
	 */
	protected function addLocaleCurrencyData( \Aimeos\MShop\Common\Manager\Iface $localeManager, array $data )
	{
		$this->info( 'Adding data for MShop locale currencies', 'vv', 1 );

		$manager = $localeManager->getSubManager( 'currency', 'Standard' );
		$items = $manager->search( $manager->filter()->slice( 0, 0x7fffffff ) );

		foreach( $data as $key => $dataset ) {
			$items->has( $key ) ?: $manager->save( $manager->create()->fromArray( $dataset, true ) );
		}
	}


	/**
	 * Adds locale language data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param array $data Associative list of locale language data
	 */
	protected function addLocaleLanguageData( \Aimeos\MShop\Common\Manager\Iface $localeManager, array $data )
	{
		$this->info( 'Adding data for MShop locale languages', 'vv', 1 );

		$manager = $localeManager->getSubManager( 'language', 'Standard' );
		$items = $manager->search( $manager->filter()->slice( 0, 0x7fffffff ) );

		foreach( $data as $key => $dataset ) {
			$items->has( $key ) ?: $manager->save( $manager->create()->fromArray( $dataset, true ) );
		}
	}


	/**
	 * Adds locale data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param array $data Associative list of locale data
	 */
	protected function addLocaleData( \Aimeos\MShop\Common\Manager\Iface $localeManager, array $data, array $siteIds )
	{
		$this->info( 'Adding data for MShop locales', 'vv', 1 );

		$map = [];

		foreach( $data as $dataset )
		{
			if( !isset( $siteIds[$dataset['site']] ) ) {
				throw new \RuntimeException( sprintf( 'No ID for site for key "%1$s" found', $dataset['site'] ) );
			}

			$map[$siteIds[$dataset['site']]['site']][] = $dataset;
		}

		foreach( $map as $siteId => $datasets )
		{
			$this->context()->setLocale( $localeManager->create()->setSiteId( $siteId ) );

			if( !$localeManager->search( $localeManager->filter() )->isEmpty() ) {
				continue;
			}

			foreach( $datasets as $dataset ) {
				$localeManager->save( $localeManager->create()->fromArray( $dataset, true ) );
			}
		}
	}
}
