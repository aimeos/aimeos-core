<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Abstract
{
	private $_context = null;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
	}


	/**
	 * Creates a new locale object and adds this object to the context.
	 *
	 * @param string $site Site code
	 * @param string|null $langid Two letter ISO code for language
	 * @param string|null $currencyid Three letter ISO code for currency
	 */
	protected function _setLocale( $site, $langid = null, $currencyid = null )
	{
		$manager = MShop_Factory::createManager( $this->_context, 'locale' );

		$localeItem = $manager->bootstrap( $site, $langid, $currencyid, false );
		$localeItem->setLanguageId( $langid );
		$localeItem->setCurrencyId( $currencyid );

		$this->_context->setLocale( $localeItem );
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop context object implementing MShop_Context_Item_Interface
	 */
	protected function _getContext()
	{
		return $this->_context;
	}
}
