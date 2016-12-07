<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @param \Aimeos\MW\Cache\Iface $cache Cache object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @param \Aimeos\MW\DB\Manager\Iface $dbManager Database manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $dbManager );

	/**
	 * Returns the database manager object.
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public function getDatabaseManager();

	/**
	 * Sets the file system manager object.
	 *
	 * @param \Aimeos\MW\Filesystem\Manager\Iface $fsManager File system manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setFilesystemManager( \Aimeos\MW\Filesystem\Manager\Iface $fsManager );

	/**
	 * Returns the file system manager object.
	 *
	 * @return \Aimeos\MW\Filesystem\Manager\Iface File system manager object
	 */
	public function getFilesystemManager();

	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 */
	public function getFilesystem( $resource );

	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param array $translations Associative list of internationalization objects implementing
	 * 	\Aimeos\MW\Translation\Iface with locale as key
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setI18n( array $translations );

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\MW\Translation\Iface Internationalization object
	 */
	public function getI18n( $locale = null );

	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setLogger( \Aimeos\MW\Logger\Iface $logger );

	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\MW\Logger\Iface Logger object
	 */
	public function getLogger();

	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\MW\Mail\Iface $mail Mail object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMail( \Aimeos\MW\Mail\Iface $mail );

	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\MW\Mail\Iface Mail object
	 */
	public function getMail();

	/**
	 * Sets the message queue manager object.
	 *
	 * @param \Aimeos\MW\MQueue\Manager\Iface $mqManager Message queue manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMessageQueueManager( \Aimeos\MW\MQueue\Manager\Iface $mqManager );

	/**
	 * Returns the message queue manager object.
	 *
	 * @return \Aimeos\MW\MQueue\Manager\Iface Message queue manager object
	*/
	public function getMessageQueueManager();

	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @apram string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\MW\Queue\Manager\Iface Message queue object
	 */
	public function getMessageQueue( $resource, $queue );

	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setSession( \Aimeos\MW\Session\Iface $session );

	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\MW\Session\Iface Session object
	*/
	public function getSession();

	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @param \Closure|string|null $user User ID of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
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
	 * @param \Closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setGroupIds( $groupIds );


	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function getGroupIds();
}
