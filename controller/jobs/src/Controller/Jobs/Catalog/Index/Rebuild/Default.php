<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Rebuild catalog index job controller.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Catalog_Index_Rebuild_Default
	extends Controller_Jobs_Abstract
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Catalog index rebuild' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Rebuilds the catalog index for searching products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = clone $this->_getContext();
		$sitecode = $context->getConfig()->get( 'controller/jobs/catalog/index/rebuild/sites', array( 'default' ) );

		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
		$siteManager = $localeManager->getSubManager( 'site' );

		$criteria = $siteManager->createSearch( true );
		$expr = array(
			$criteria->getConditions(),
			$criteria->compare( '==', 'locale.site.code', $sitecode ),
		);
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$start = 0;

		do
		{

			$items = $siteManager->searchItems( $criteria );

			foreach( $items as $item )
			{
				try
				{
					$locale = $localeManager->bootstrap( $item->getCode() );

					$locale->setLanguageId( null );
					$locale->setCurrencyId( null );
					$context->setLocale( $locale );

					$manager = MShop_Catalog_Manager_Factory::createManager( $context )->getSubManager( 'index' );
					$manager->rebuildIndex();
					$manager->optimize();
				}
				catch( Exception $e )
				{
					$str = 'Error processing site "%1$s" in "%2$s: %3$s';
					$context->getLogger()->log( sprintf( $str, $item->getCode(), __CLASS__, $e->getMessage() ) );
				}
			}

			$count = count( $items );
			$start += $count;
			$criteria->setSlice( $start );
		}
		while( $count > $criteria->getSliceSize() );
	}
}
