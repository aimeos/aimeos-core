<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * Returns the cache object.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function cache() : \Aimeos\MW\Cache\Iface;

	/**
	 * Returns the cache object.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	public function getCache() : \Aimeos\MW\Cache\Iface;

	/**
	 * Sets the cache object.
	 *
	 * @param \Aimeos\MW\Cache\Iface $cache Cache object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setCache( \Aimeos\MW\Cache\Iface $cache ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	public function config() : \Aimeos\MW\Config\Iface;

	/**
	 * Returns the configuration object.
	 *
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	public function getConfig() : \Aimeos\MW\Config\Iface;

	/**
	 * Sets the configuration object.
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setConfig( \Aimeos\MW\Config\Iface $config ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the database manager object.
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public function db() : \Aimeos\MW\DB\Manager\Iface;

	/**
	 * Returns the database manager object.
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public function getDatabaseManager() : \Aimeos\MW\DB\Manager\Iface;

	/**
	 * Sets the database connection manager object.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbManager Database manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setDatabaseManager( \Aimeos\MW\DB\Manager\Iface $dbManager ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the current date and time
	 * This is especially useful to share the same request time or if applications
	 * allow to travel in time.
	 *
	 * @return string Current date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function datetime() : string;

	/**
	 * Returns the current date and time
	 * This is especially useful to share the same request time or if applications
	 * allow to travel in time.
	 *
	 * @return string Current date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function getDateTime() : string;

	/**
	 * Sets the current date and time
	 *
	 * @param string $datetime Date and time as ISO string (YYYY-MM-DD HH:mm:ss)
	 */
	public function setDateTime( string $datetime ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 */
	public function fs( string $resource ) : \Aimeos\MW\Filesystem\Iface;

	/**
	 * Returns the file system object for the given resource name.
	 *
	 * @param string $resource Resource name, e.g. "fs-admin"
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 */
	public function getFilesystem( string $resource ) : \Aimeos\MW\Filesystem\Iface;

	/**
	 * Returns the file system manager object.
	 *
	 * @return \Aimeos\MW\Filesystem\Manager\Iface File system manager object
	 */
	public function getFilesystemManager() : \Aimeos\MW\Filesystem\Manager\Iface;

	/**
	 * Sets the file system manager object.
	 *
	 * @param \Aimeos\MW\Filesystem\Manager\Iface $fsManager File system manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setFilesystemManager( \Aimeos\MW\Filesystem\Manager\Iface $fsManager ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\MW\Translation\Iface Internationalization object
	 */
	public function i18n( string $locale = null ) : \Aimeos\MW\Translation\Iface;

	/**
	 * Returns the translation/internationalization object for the given locale (null for default one).
	 *
	 * @param string|null $locale Two letter language ISO code for specific language instead of default one
	 * @return \Aimeos\MW\Translation\Iface Internationalization object
	 */
	public function getI18n( string $locale = null ) : \Aimeos\MW\Translation\Iface;

	/**
	 * Sets the translation/internationalization objects.
	 *
	 * @param \Aimeos\MW\Translation\Iface[] $translations Associative list locale as key as items as values
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setI18n( array $translations ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Translates a string if possible
	 *
	 * @param string $name Name of the translation domain
	 * @param string $singular Singular string to translate
	 * @param string $plural Plural string to translate if count is not one
	 * @param int $number Number for plural translations
	 * @param string|null $locale Locale (e.g. en, en_US, de, etc.) or NULL for current locale
	 * @return string Translated string if possible
	 */
	public function translate( string $domain, string $singular, string $plural = null, int $number = 1, string $locale = null ) : string;

	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function locale() : \Aimeos\MShop\Locale\Item\Iface;

	/**
	 * Returns the localization object.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Localization object
	 */
	public function getLocale() : \Aimeos\MShop\Locale\Item\Iface;

	/**
	 * Sets the localization object.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Localization object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\MW\Logger\Iface Logger object
	 */
	public function logger() : \Aimeos\MW\Logger\Iface;

	/**
	 * Returns the logger object.
	 *
	 * @return \Aimeos\MW\Logger\Iface Logger object
	 */
	public function getLogger() : \Aimeos\MW\Logger\Iface;

	/**
	 * Sets the logger object.
	 *
	 * @param \Aimeos\MW\Logger\Iface $logger Logger object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setLogger( \Aimeos\MW\Logger\Iface $logger ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\MW\Mail\Iface Mail object
	 */
	public function mail() : \Aimeos\MW\Mail\Iface;

	/**
	 * Returns the mail object.
	 *
	 * @return \Aimeos\MW\Mail\Iface Mail object
	 */
	public function getMail() : \Aimeos\MW\Mail\Iface;

	/**
	 * Sets the mail object.
	 *
	 * @param \Aimeos\MW\Mail\Iface $mail Mail object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMail( \Aimeos\MW\Mail\Iface $mail ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the password adapter object.
	 *
	 * @return \Aimeos\MW\Password\Iface Password adapter
	 */
	public function password() : \Aimeos\MW\Password\Iface;

	/**
	 * Sets the password adapter object.
	 *
	 * @param \Aimeos\MW\Password\Iface $password Password adapter
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setPassword( \Aimeos\MW\Password\Iface $password ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @param string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue object
	 */
	public function queue( string $resource, string $queue ) : \Aimeos\MW\MQueue\Queue\Iface;

	/**
	 * Returns the message queue object.
	 *
	 * @param string $resource Resource name, e.g. "mq-email"
	 * @param string $queue Message queue name, e.g. "order/email/payment"
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue object
	 */
	public function getMessageQueue( string $resource, string $queue ) : \Aimeos\MW\MQueue\Queue\Iface;

	/**
	 * Returns the message queue manager object.
	 *
	 * @return \Aimeos\MW\MQueue\Manager\Iface Message queue manager object
	 */
	public function getMessageQueueManager() : \Aimeos\MW\MQueue\Manager\Iface;

	/**
	 * Sets the message queue manager object.
	 *
	 * @param \Aimeos\MW\MQueue\Manager\Iface $mqManager Message queue manager object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setMessageQueueManager( \Aimeos\MW\MQueue\Manager\Iface $mqManager ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the nonce value for inline Javascript
	 *
	 * @return string|null Nonce value
	 */
	public function nonce() : ?string;

	/**
	 * Sets the nonce value for inline Javascript
	 *
	 * @param string|null $value Nonce value
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setNonce( ?string $value ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the process object.
	 *
	 * @return \Aimeos\MW\Process\Iface Process object
	 */
	public function process() : \Aimeos\MW\Process\Iface;

	/**
	 * Returns the process object.
	 *
	 * @return \Aimeos\MW\Process\Iface Process object
	 */
	public function getProcess() : \Aimeos\MW\Process\Iface;

	/**
	 * Sets the process object.
	 *
	 * @param \Aimeos\MW\Process\Iface $process Process object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setProcess( \Aimeos\MW\Process\Iface $process ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\MW\Session\Iface Session object
	 */
	public function session() : \Aimeos\MW\Session\Iface;

	/**
	 * Returns the session object.
	 *
	 * @return \Aimeos\MW\Session\Iface Session object
	 */
	public function getSession() : \Aimeos\MW\Session\Iface;

	/**
	 * Sets the session object.
	 *
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setSession( \Aimeos\MW\Session\Iface $session ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\MW\View\Iface View object
	 */
	public function view() : \Aimeos\MW\View\Iface;

	/**
	 * Returns the view object.
	 *
	 * @return \Aimeos\MW\View\Iface View object
	 */
	public function getView() : \Aimeos\MW\View\Iface;

	/**
	 * Sets the view object.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name of the user/editor
	 */
	public function editor() : string;

	/**
	 * Returns the account name of the user/editor.
	 *
	 * @return string Account name or IP address of the user/editor
	 */
	public function getEditor() : string;

	/**
	 * Sets the account name of the user/editor.
	 *
	 * @param string $name Account name or IP address of the user/editor
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setEditor( string $name ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string|null User ID of the logged in user
	 */
	public function user() : ?string;

	/**
	 * Returns the user ID of the logged in user.
	 *
	 * @return string|null User ID of the logged in user
	 */
	public function getUserId() : ?string;

	/**
	 * Sets the user ID of the logged in user.
	 *
	 * @param \Closure|string|null $user User ID of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setUserId( $user ) : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function groups() : array;

	/**
	 * Returns the group IDs of the logged in user.
	 *
	 * @return array Group IDs of the logged in user
	 */
	public function getGroupIds() : array;

	/**
	 * Sets the group IDs of the logged in user.
	 *
	 * @param \Closure|array $groupIds Group IDs of the logged in user or closure to retrieve them
	 * @return \Aimeos\MShop\Context\Item\Iface Context item for chaining method calls
	 */
	public function setGroupIds( $groupIds ) : \Aimeos\MShop\Context\Item\Iface;
}
