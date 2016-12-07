<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Log messages to the error_log file.
 *
 * @package MW
 * @subpackage Logger
 */
class Errorlog extends \Aimeos\MW\Logger\Base implements \Aimeos\MW\Logger\Iface
{
	private $loglevel;
	private $facilities;


	/**
	 * Initializes the logger object.
	 *
	 * @param integer Log level from \Aimeos\MW\Logger\Base
	 * @param array|null $facilities Facilities for which messages should be logged
	 */
	public function __construct( $loglevel = \Aimeos\MW\Logger\Base::ERR, array $facilities = null )
	{
		$this->loglevel = $loglevel;
		$this->facilities = $facilities;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws \Aimeos\MW\Logger\Exception If the priority is invalid
	 * @see \Aimeos\MW\Logger\Base for available log level constants
	 */
	public function log( $message, $priority = \Aimeos\MW\Logger\Base::ERR, $facility = 'message' )
	{
		if( $priority <= $this->loglevel
			&& ( $this->facilities === null || in_array( $facility, $this->facilities ) ) )
		{
			switch( $priority )
			{
				// @codingStandardsIgnoreStart
				case \Aimeos\MW\Logger\Base::EMERG: $level = '[emergency]'; break;
				case \Aimeos\MW\Logger\Base::ALERT: $level = '[alert]'; break;
				case \Aimeos\MW\Logger\Base::CRIT: $level = '[critical]'; break;
				case \Aimeos\MW\Logger\Base::ERR: $level = '[error]'; break;
				case \Aimeos\MW\Logger\Base::WARN: $level = '[warning]'; break;
				case \Aimeos\MW\Logger\Base::NOTICE: $level = '[notice]'; break;
				case \Aimeos\MW\Logger\Base::INFO: $level = '[info]'; break;
				case \Aimeos\MW\Logger\Base::DEBUG: $level = '[debug]'; break;
				// @codingStandardsIgnoreEnd
				default:
					throw new \Aimeos\MW\Logger\Exception( sprintf( 'Invalid log priority %1$s', $priority ) );
			}

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			if( error_log( '<' . $facility . '> ' . $level . ' ' . $message ) === false ) {
				throw new \Aimeos\MW\Logger\Exception( sprintf(
					'Unable to log message with priority "%1$d": %2$s', $priority, $message ) );
			}
		}
	}
}
