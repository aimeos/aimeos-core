<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Context
 */


namespace Aimeos\MShop\Context\Item;


/**
 * Common objects which have to be available for all manager objects.
 *
 * @package MShop
 * @subpackage Context
 */
interface Iface
{
	/**
	 * Sets the cache object.
	 *
	 * @param \Aimeos\MW\Cache\Iface $cache Cahce object
	 * @return void
	 */
	public function setCache( \Aimeos\MW\Cache\Iface $cache );

	/**
	 * Returns the cache object.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache();

	/**
	 * Sets the configuration object.
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @return void
	 */
	public function setConfig( \Aimeos\MW\Config\Iface $config );

	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\MShop\Config\Iface Configuration object
	 */
	public function getConfig();

	/**
	 * Sets the database connection manager object.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $databaseManager Database manager object
	 * @return void
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $databaseManager );

	/**
	 * Returns the database manager object.
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public function getDatabaseManager();

	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	\Aimeos\MW\Translation\Iface with locale as key
	 * @return void
	 */
	public function setI18n( array $translations );

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\MW\Translation\Iface Internationalization object
	 */
	public function getI18n( $locale = null );

	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return void
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale );

	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function getLocale();

	/**
	 * Sets the logger object.
	 *
	 * @param \Aimeos\MW\Logger\Iface $logger Logger object
	 * @return void
	 */
	public function setLogger( \Aimeos\MW\Logger\Iface $logger );

	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\MW\Logger\Iface Logger object
	 */
	public function getLogger();

	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 * @return void
	 */
	public function setSession( \Aimeos\MW\Session\Iface $session );

	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\MW\Session\Iface Session object
	 */
	public function getSession();

	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\MW\Mail\Iface $mail Mail object
	 * @return void
	 */
	public function setMail( \Aimeos\MW\Mail\Iface $mail );

	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\MW\Mail\Iface Mail object
	 */
	public function getMail();

	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return void
	 */
	public function setView( \Aimeos\MW\View\Iface $view );

	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\MW\View\Iface View object
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
