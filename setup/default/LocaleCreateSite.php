<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds locale records to tables.
 */
class LocaleCreateSite extends Base
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
		return ['MShopAddLocaleData'];
	}


	/**
	 * Adds locale data.
	 */
	public function up()
	{
		$this->info( 'Create site and locale', 'vv' );

		$context = $this->context()->setEditor( 'core' ); // Set editor for further tasks

		$config = $context->config();
		$site = $config->get( 'setup/site', 'default' );
		$lang = $config->get( 'setup/language', 'en' );
		$curr = $config->get( 'setup/currency', 'USD' );
		$demo = $config->get( 'setup/default/demo', '' );

		$siteId = $this->createSite( $site );
		$this->createLocale( $siteId, ['locale.languageid' => $lang, 'locale.currencyid' => $curr] );

		if( $demo === '1' )
		{
			foreach( $this->data() as $entry ) {
				$this->createLocale( $siteId, $entry );
			}
		}
	}


	protected function createSite( string $code ) : string
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'locale', 'Standard' )->getSubManager( 'site', 'Standard' );

		try
		{
			$item = $manager->find( $code );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$manager->begin();
			$item = $manager->insert( $manager->create()->setCode( $code )->setLabel( ucfirst( $code ) ) );
			$manager->commit();
		}

		return $item->getSiteId();
	}


	protected function createLocale( string $siteId, array $data )
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'locale', 'Standard' );

		$context->setLocale( $manager->create()->setSiteId( $siteId ) );

		try {
			$manager->save( $manager->create()->fromArray( $data ) );
		} catch( \Aimeos\Base\DB\Exception $e ) {
		}
	}


	protected function data() : array
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'locale.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		return $data;
	}
}
