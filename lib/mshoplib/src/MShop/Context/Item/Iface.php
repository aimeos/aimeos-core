<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Context
 */


/**
 * Common objects which have to be available for all manager objects.
 *
 * @package MShop
 * @subpackage Context
 */
interface MShop_Context_Item_Iface
{
	/**
	 * Sets the cache object.
	 *
	 * @param MW_Cache_Iface $cache Cahce object
	 * @return void
	 */
	public function setCache( MW_Cache_Iface $cache );

	/**
	 * Returns the cache object.
	 *
	 * @return MW_Cache_Iface Cache object
	 */
	public function getCache();

	/**
	 * Sets the configuration object.
	 *
	 * @param MW_Config_Iface $config Configuration object
	 * @return void
	 */
	public function setConfig( MW_Config_Iface $config );

	/**
	 * Returns the configuration object.
	 *
	 * @return MShop_Config_Iface Configuration object
	 */
	public function getConfig();

	/**
	 * Sets the database connection manager object.
	 *
	 * @param MW_DB_Manager_Iface $databaseManager Database manager object
	 * @return void
	 */
	public function setDatabaseManager( MW_DB_Manager_Iface $databaseManager );

	/**
	 * Returns the database manager object.
	 *
	 * @return MW_DB_Manager_Iface Database manager object
	 */
	public function getDatabaseManager();

	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	MW_Translation_Iface with locale as key
	 * @return void
	 */
	public function setI18n( array $translations );

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string $locale Two letter language ISO code for specific language instead of default one
	 * @return MW_Translation_Iface Internationalization object
	 */
	public function getI18n( $locale = null );

	/**
	 * Sets the localization object.
	 *
	 * @param MShop_Locale_Item_Iface $locale Localization object
	 * @return void
	 */
	public function setLocale( MShop_Locale_Item_Iface $locale );

	/**
	 * Returns the localization object.
	 *
	 * @return MShop_Locale_Item_Iface Localization object
	 */
	public function getLocale();

	/**
	 * Sets the logger object.
	 *
	 * @param MW_Logger_Iface $logger Logger object
	 * @return void
	 */
	public function setLogger( MW_Logger_Iface $logger );

	/**
	 * Returns the logger object.
	 *
	 * @return MW_Logger_Iface Logger object
	 */
	public function getLogger();

	/**
	 * Sets the session object.
	 *
	 * @param MW_Session_Iface $session Session object
	 * @return void
	 */
	public function setSession( MW_Session_Iface $session );

	/**
	 * Returns the session object.
	 *
	 * @return MW_Session_Iface Session object
	 */
	public function getSession();

	/**
	 * Sets the mail object.
	 *
	 * @param MW_Mail_Iface $mail Mail object
	 * @return void
	 */
	public function setMail( MW_Mail_Iface $mail );

	/**
	 * Returns the mail object.
	 *
	 * @return MW_Mail_Iface Mail object
	 */
	public function getMail();

	/**
	 * Sets the view object.
	 *
	 * @param MW_View_Iface $view View object
	 * @return void
	 */
	public function setView( MW_View_Iface $view );

	/**
	 * Returns the view object.
	 *
	 * @return MW_View_Iface View object
	 */
	public function getView();

	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name of the user/editor
	 * @return void
	 */
	public function setEditor( $name );

	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function getEditor();

	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param string $user User ID of the logged in user or closure to retrieve them
	 * @return void
	 */
	public function setUserId( $user );

	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string User ID of the logged in user
	 */
	public function getUserId();


	/**
	 * Sets the group IDs of the logged in user.
	 *
	 * @param closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 * @return void
	 */
	public function setGroupIds( $groupIds );


	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function getGroupIds();
}
