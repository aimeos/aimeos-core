<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages to the error_log file.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_Errorlog extends MW_Logger_Base implements MW_Logger_Iface
{
	private $loglevel;
	private $facilities;


	/**
	 * Initializes the logger object.
	 *
	 * @param integer Log level from MW_Logger_Base
	 * @param array|null $facilities Facilities for which messages should be logged
	 */
	public function __construct( $loglevel = MW_Logger_Base::ERR, array $facilities = null )
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
	 * @throws MW_Logger_Exception If the priority is invalid
	 * @see MW_Logger_Base for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Base::ERR, $facility = 'message' )
	{
		if( $priority <= $this->loglevel
			&& ( $this->facilities === null || in_array( $facility, $this->facilities ) ) )
		{
			switch( $priority )
			{
				// @codingStandardsIgnoreStart
				case MW_Logger_Base::EMERG: $level = '[emergency]'; break;
				case MW_Logger_Base::ALERT: $level = '[alert]'; break;
				case MW_Logger_Base::CRIT: $level = '[critical]'; break;
				case MW_Logger_Base::ERR: $level = '[error]'; break;
				case MW_Logger_Base::WARN: $level = '[warning]'; break;
				case MW_Logger_Base::NOTICE: $level = '[notice]'; break;
				case MW_Logger_Base::INFO: $level = '[info]'; break;
				case MW_Logger_Base::DEBUG: $level = '[debug]'; break;
				// @codingStandardsIgnoreEnd
				default:
					throw new MW_Logger_Exception( sprintf( 'Invalid log priority %1$s', $priority ) );
			}

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			if( error_log( '<' . $facility . '> ' . $level . ' ' . $message ) === false ) {
				throw new MW_Logger_Exception( sprintf(
					'Unable to log message with priority "%1$d": %2$s', $priority, $message ) );
			}
		}
	}
}
