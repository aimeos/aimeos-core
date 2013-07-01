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
		$context = Zend_Registry::get( 'ctx' );
		$codes = $context->getConfig()->get( 'zfapp/sites', array( 'default', 'unittest', 'unitperf' ) );

		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
		$siteManager = $localeManager->getSubManager('site');

		$search = $siteManager->createSearch( (APPLICATION_ENV=='development'?false:true) );
		$expr = array(
			$search->compare( '==', 'locale.site.code', $codes ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$data = array();
		foreach( $siteManager->searchItems( $search ) AS $item )
		{
			if ( $item->getStatus() > 0 || APPLICATION_ENV == 'development' ) {
				$data[ $item->getCode() ] = $item->getLabel();
			}
		}

		return $data;
	}

}
