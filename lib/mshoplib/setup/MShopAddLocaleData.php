<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds locale records to tables.
 */
class MShopAddLocaleData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopAddLocaleLangCurData', 'TablesCreateMAdmin'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Creates new locale data if necessary
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding locale data if not yet present', 0 );


		// Set editor for further tasks
		$this->additional->setEditor( 'core:setup' );


		$code = $this->additional->getConfig()->get( 'setup/site', 'default' );

		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( $this->additional, 'Standard' );
		$siteManager = $localeManager->getSubManager( 'site' );

		try
		{
			$siteItem = $siteManager->create();
			$siteItem->setLabel( $code );
			$siteItem->setCode( $code );
			$siteItem->setStatus( 1 );

			$siteManager->insert( $siteItem );
		}
		catch( \Aimeos\MW\DB\Exception $e ) // already in the database
		{
			$siteItem = $siteManager->find( $code );
		}

		try
		{
			$localeItem = $localeManager->create();
			$localeItem->setSiteId( $siteItem->getSiteId() );
			$localeItem->setLanguageId( 'en' );
			$localeItem->setCurrencyId( 'EUR' );
			$localeItem->setStatus( 1 );

			$localeManager->save( $localeItem, false );
		}
		catch( \Aimeos\MW\DB\Exception $e ) // already in the database
		{
			$this->status( 'OK' );
			return;
		}

		$this->status( 'done' );
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
		$this->msg( 'Adding data for MShop locale sites', 1 );

		$localeSiteManager = $localeManager->getSubManager( 'site', $manager );
		$siteItem = $localeSiteManager->create();
		$siteIds = [];

		foreach( $data as $key => $dataset )
		{
			try
			{
				$siteItem->setId( null );
				$siteItem->setCode( $dataset['code'] );
				$siteItem->setLabel( $dataset['label'] );
				$siteItem->setConfig( $dataset['config'] );
				$siteItem->setStatus( $dataset['status'] );
				$siteItem->setSupplierId( $dataset['supplierid'] ?? '' );
				$siteItem->setTheme( $dataset['theme'] ?? '' );
				$siteItem->setLogos( $dataset['logo'] ?? [] );
				$siteItem->setIcon( $dataset['icon'] ?? '' );

				$localeSiteManager->insert( $siteItem, $parentId );
				$siteIds[$key] = $siteItem->getSiteId();
			}
			catch( \Aimeos\MW\DB\Exception $e )
			{
				$search = $localeSiteManager->filter();
				$search->setConditions( $search->compare( '==', 'locale.site.code', $dataset['code'] ) );
				$result = $localeSiteManager->search( $search );

				if( ( $item = $result->first() ) === null ) {
					throw new \RuntimeException( sprintf( 'No site for code "%1$s" available', $dataset['code'] ) );
				}

				$siteIds[$key] = $item->getSiteId();
			}
		}

		$this->status( 'done' );

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
		$this->msg( 'Adding data for MShop locale currencies', 1 );

		$currencyManager = $localeManager->getSubManager( 'currency', 'Standard' );
		$items = $currencyManager->search( $currencyManager->filter()->slice( 0, 0x7fffffff ) );

		$num = $total = 0;

		foreach( $data as $key => $dataset )
		{
			$total++;

			if( !isset( $items[$dataset['id']] ) )
			{
				$currencyItem = $currencyManager->create();
				$currencyItem->setCode( $dataset['id'] );
				$currencyItem->setLabel( $dataset['label'] );
				$currencyItem->setStatus( $dataset['status'] );

				$items[$dataset['id']] = $currencyItem;
			}

			$currencyManager->save( $items[$dataset['id']] );
			$num++;
		}

		$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
	}


	/**
	 * Adds locale language data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeManager Locale manager
	 * @param array $data Associative list of locale language data
	 */
	protected function addLocaleLanguageData( \Aimeos\MShop\Common\Manager\Iface $localeManager, array $data )
	{
		$this->msg( 'Adding data for MShop locale languages', 1 );

		$languageManager = $localeManager->getSubManager( 'language', 'Standard' );
		$items = $languageManager->search( $languageManager->filter()->slice( 0, 0x7fffffff ) );

		$num = $total = 0;

		foreach( $data as $dataset )
		{
			$total++;

			if( !isset( $items[$dataset['id']] ) )
			{
				$languageItem = $languageManager->create();
				$languageItem->setCode( $dataset['id'] );
				$languageItem->setLabel( $dataset['label'] );
				$languageItem->setStatus( $dataset['status'] );

				$items[$dataset['id']] = $languageItem;
			}

			$languageManager->save( $items[$dataset['id']] );
			$num++;
		}

		$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
	}


	/**
	 * Adds locale data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $localeItemManager Locale manager
	 * @param array $data Associative list of locale data
	 */
	protected function addLocaleData( \Aimeos\MShop\Common\Manager\Iface $localeItemManager, array $data, array $siteIds )
	{
		$this->msg( 'Adding data for MShop locales', 1 );

		$localeItem = $localeItemManager->create();

		foreach( $data as $key => $dataset )
		{
			if( !isset( $siteIds[$dataset['siteid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No ID for site for key "%1$s" found', $dataset['siteid'] ) );
			}

			$localeItem->setId( null );
			$localeItem->setSiteId( $siteIds[$dataset['siteid']] );
			$localeItem->setLanguageId( $dataset['langid'] );
			$localeItem->setCurrencyId( $dataset['currencyid'] );
			$localeItem->setPosition( $dataset['pos'] );
			$localeItem->setStatus( $dataset['status'] );

			try {
				$localeItemManager->save( $localeItem );
			} catch( \Aimeos\MW\DB\Exception $e ) {; } // if locale combination was already available
		}

		$this->status( 'done' );
	}
}
