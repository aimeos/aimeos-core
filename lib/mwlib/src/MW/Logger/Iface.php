<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Generic minimal interface for logging messages
 *
 * @package MW
 * @subpackage Logger
 */
interface Iface
{
	/**
	 * Emergency (0): system is unusable
	 */
	const EMERG = 0;

	/**
	 * Alert (1): action must be taken immediately
	 */
	const ALERT = 1;

	/**
	 * Critical (2): critical conditions
	 */
	const CRIT = 2;

	/**
	 * Error (3): error conditions
	 */
	const ERR = 3;

	/**
	 * Warning (4): warning conditions
	 */
	const WARN = 4;

	/**
	 * Notice (5): normal but significant condition
	 */
	const NOTICE = 5;

	/**
	 * Informational (6): informational messages
	 */
	const INFO = 6;

	/**
	 * Debug (7): debug messages
	 */
	const DEBUG = 7;


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param int $prio Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function log( $message, int $prio = Iface::ERR, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "emergency" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function emergency( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "critical" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function critical( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "alert" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function alert( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "error" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function error( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "warning" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function warning( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "notice" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function notice( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "info" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function info( $message, string $facility = 'message' ) : Iface;

	/**
	 * Write as message of severity "debug" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function debug( $message, string $facility = 'message' ) : Iface;
}
