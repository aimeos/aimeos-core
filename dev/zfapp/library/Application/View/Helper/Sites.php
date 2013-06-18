<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 *  Category helper
 */
class Application_View_Helper_Sites extends Zend_View_Helper_Abstract
{
	/**
	 * Returns a list of active sites.
	 * In development mode (APPLICATION_ENV) all sites will be returned.
	 *
	 * @return array List of key=>label pairs of active sites.
	 */
	public function sites()
	{
		$data = array();

		$localeManager = MShop_Locale_Manager_Factory::createManager( Zend_Registry::get( 'ctx' ) );
		$siteManager = $localeManager->getSubManager('site');

		$search = $siteManager->createSearch( (APPLICATION_ENV=='development'?false:true) );

		$expr[] = $search->compare( '>', 'locale.site.code', '' );
		$expr[] = $search->compare( '==', 'locale.site.level', 0 );
		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $siteManager->searchItems( $search );

		$data = array();
		foreach ($results AS $item) {
			if ( $item->getStatus() > 0 || APPLICATION_ENV == 'development' ) {
				$data[ $item->getCode() ] = $item->getLabel();
			}
		}

		return $data;
	}

}
