<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Base logger class defining required error level constants
 *
 * @package MW
 * @subpackage Logger
 */
trait Traits
{
	/**
	 * Write as message of severity "emergency" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function emergency( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::EMERG, $facility );
	}


	/**
	 * Write as message of severity "critical" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function critical( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::CRIT, $facility );
	}


	/**
	 * Write as message of severity "alert" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function alert( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::ALERT, $facility );
	}


	/**
	 * Write as message of severity "error" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function error( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::ERR, $facility );
	}


	/**
	 * Write as message of severity "warning" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function warning( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::WARN, $facility );
	}


	/**
	 * Write as message of severity "notice" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function notice( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::NOTICE, $facility );
	}


	/**
	 * Write as message of severity "info" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function info( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::INFO, $facility );
	}


	/**
	 * Write as message of severity "debug" to the log.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function debug( $message, string $facility = 'message' ) : Iface
	{
		return $this->log( $message, Iface::DEBUG, $facility );
	}


	/**
	 * Checks if the given log constant is valid
	 *
	 * @param int $level Log constant
	 * @return mixed Log level
	 * @throws \Aimeos\MW\Logger\Exception If log constant is unknown
	 */
	protected function getLogLevel( int $level )
	{
		switch( $level )
		{
			case Iface::EMERG: return 'emergency';
			case Iface::ALERT: return 'alert';
			case Iface::CRIT: return 'critical';
			case Iface::ERR: return 'error';
			case Iface::WARN: return 'warning';
			case Iface::NOTICE: return 'notice';
			case Iface::INFO: return 'info';
			case Iface::DEBUG: return 'debug';
		}

		throw new \Aimeos\MW\Logger\Exception( sprintf( 'Invalid log level constant "%1$d"', $level ) );
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param int $prio Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	abstract public function log( $message, int $prio = Iface::ERR, string $facility = 'message' ) : Iface;
}
