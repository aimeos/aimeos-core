<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Context
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common objects which have to be available for all manager objects.
 *
 * @package MShop
 * @subpackage Context
 */
interface MShop_Context_Item_Interface
{
	/**
	 * Sets the cache object.
	 *
	 * @param MW_Cache_Interface $cache Cahce object
	 */
	public function setCache( MW_Cache_Interface $cache );

	/**
	 * Returns the cache object.
	 *
	 * @return MW_Cache_Interface Cache object
	 */
	public function getCache();

	/**
	 * Sets the configuration object.
	 *
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function setConfig( MW_Config_Interface $config );

	/**
	 * Returns the configuration object.
	 *
	 * @return MShop_Config_Interface Configuration object
	 */
	public function getConfig();

	/**
	 * Sets the database connection manager object.
	 *
	 * @param MW_DB_Manager_Interface $databaseManager Database manager object
	 */
	public function setDatabaseManager( MW_DB_Manager_Interface $databaseManager );

	/**
	 * Returns the database manager object.
	 *
	 * @return MW_DB_Manager_Interface Database manager object
	 */
	public function getDatabaseManager();

	/**
	 * Sets the internationalization object.
	 *
	 * @param MW_Translation_Interface $translate Internationalization object
	 */
	public function setI18n( MW_Translation_Interface $translate );

	/**
	 * Returns the internationalization object.
	 *
	 * @return MW_Translation_Interface Internationalization object
	 */
	public function getI18n();

	/**
	 * Sets the localization object.
	 *
	 * @param MShop_Locale_Item_Interface $locale Localization object
	 */
	public function setLocale( MShop_Locale_Item_Interface $locale );

	/**
	 * Returns the localization object.
	 *
	 * @return MShop_Locale_Item_Interface Localization object
	 */
	public function getLocale();

	/**
	 * Sets the logger object.
	 *
	 * @param MW_Logger_Interface $logger Logger object
	 */
	public function setLogger( MW_Logger_Interface $logger );

	/**
	 * Returns the logger object.
	 *
	 * @return MW_Logger_Interface Logger object
	 */
	public function getLogger();

	/**
	 * Sets the session object.
	 *
	 * @param MW_Session_Interface $session Session object
	 */
	public function setSession( MW_Session_Interface $session );

	/**
	 * Returns the session object.
	 *
	 * @return MW_Session_Interface Session object
	 */
	public function getSession();

	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name of the user/editor
	 */
	public function setEditor( $name );

	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function getEditor();
}
